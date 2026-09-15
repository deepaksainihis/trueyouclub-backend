<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon; 
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeMail;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Notification;
use App\Rules\ValidEmail;
use App\Models\Booking;
use Illuminate\Validation\Rule;


class LoginRegisterController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'                      => 'required',
            'email'                     => ['required','email:dns','unique:users,email,NULL,id,deleted_at,NULL',new ValidEmail],
            'phone'                     => 'required|numeric|regex:/^[0-9]{7,15}$/|not_in:-|unique:users,phone,NULL,id',
            // 'password'               => 'required|min:8|regex:/^(?!.*\s)(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'role'                      => [
                                            'required','numeric',
                                            Rule::exists('roles', 'id')->where(function ($query) {
                                                $query->whereNull('deleted_at')
                                                      ->where('id', '!=', config('constants.role.super_admin'));
                                            }),
                                        ],
            'password'                  => 'required|min:8',
            'password_confirmation'     => 'required|same:password',
            'location_id'               => [
                'required_if:role,' . config('constants.role.volunteer'),
                'exists:locations,id',
            ],
            // 'passcode'                  => ['required'],
        ],[
            'phone.required'=>'The phone field is required',
            'phone.regex' =>'The phone length must be 7 to 15 digits.',
            'phone.unique' =>'The phone already exists.',
            'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',

            'location_id.required_if' => 'The location is required.',
            'location_id.exists' => 'The selected location is invalid.',
        ],[
        //    'passcode'=>'golden gateway code',
        ]);
        
        if($validator->fails()){
             //Error Response Send
             $responseData = [
                'status'        => false,
                'errors' => $validator->errors(),
            ];
            return response()->json($responseData, 422);
        }

        $input = $request->all();
        
        /*
        if(!is_null($request->passcode) && !empty($request->passcode)){
            $booking = Booking::where('passcode',$request->passcode)->where('status',0)->first();
            if($booking){
                $input['booking_id'] = $booking->id;
                $booking->status = 1;
                $booking->save();
            }else{
                 //Error Response Send
                $validationMessage['passcode'] = 'Invalid golden gateway code. Please try again.'; 
                $responseData = [
                    'status'        => false,
                    'errors' =>  $validationMessage,
                ];
                return response()->json($responseData, 422);
            }
        }
        */

        DB::beginTransaction();
        try {
           
            $nameParts = explode(' ', $input['name']);
            $input['first_name'] = $nameParts[0]; 
            $input['last_name'] = isset($nameParts[1]) && (!is_null($nameParts[1])) ? $nameParts[1] : null;
        
            $input['email'] = strtolower($input['email']); 
            $input['password'] = bcrypt($input['password']);
            $input['is_active'] = 0;

         
            unset($input['role']);

            $user = User::create($input);
            if($request->role){
                $user->roles()->sync($request->role);
            }
            
            //Send welcome mail for user
            $subject = 'Welcome to ' . config('app.name');
            Mail::to($user->email)->queue(new WelcomeMail($subject, $user->name, $user->email));

            $notification_message = config('constants.user_register_notification_message');
            Notification::send($user, new UserNotification($user, $notification_message));
            
           // Verification mail sent
            $user->NotificationSendToVerifyEmail();

            
            DB::commit();

            //Success Response Send
            $responseData = [
                'status'        => true,
                'message'       => 'Registration Successfully Done! Please check your email for a verification link.',
            ];  
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'             => ['required','email',new ValidEmail],
            'password'          => 'required|min:8'
        ]);
        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'errors' => $validator->errors(),
            ];
            return response()->json($responseData, 422);
        }

        DB::beginTransaction();
        try {

            $remember_me = !is_null($request->remember) ? true : false;
            $credentialsOnly = [
                'email'    => strtolower($request->email),
                'password' => $request->password,
            ]; 

            // $checkUserStatus = User::where('email',$request->email)->withTrashed()->first();

            $checkUserStatus = User::where('email',$request->email)->first();

            if($checkUserStatus){
                // if(!is_null($checkUserStatus->deleted_at)){
                //     //Error Response Send
                //     $responseData = [
                //         'status'        => false,
                //         'error'         => 'Your account has been deactivated!',
                //     ];
                //     return response()->json($responseData, 401);
                // }

                // if(!$checkUserStatus->is_active && $checkUserStatus->is_user){
                //     //Error Response Send
                //     $responseData = [
                //         'status'        => false,
                //         'error'         => 'Your account has been blocked!',
                //     ];
                //     return response()->json($responseData, 401);
                // }

                if($checkUserStatus->is_buyer && is_null($checkUserStatus->email_verified_at)){

                    $checkUserStatus->NotificationSendToBuyerVerifyEmail();

                    //Error Response Send
                    $responseData = [
                        'status'        => false,
                        'error'         => 'Your account is not verified! Please check your mail',
                    ];
                    return response()->json($responseData, 401);
                }

            }


            if(Auth::attempt($credentialsOnly, $remember_me)){
                $user = Auth::user();

                // Check if the authenticated user has the 'user' role
                if (!($user->is_user || $user->is_volunteer)) {
                  
                    $user = $request->user();
       
                    // Revoke all user's tokens to logout
                    $user->tokens()->delete();

                    //Error Response Send
                    $responseData = [
                        'status'        => false,
                        'error'         => 'These credentials do not match our records!',
                    ];
                    return response()->json($responseData, 401);
                }

                if(is_null($user->email_verified_at)){
                    $user->NotificationSendToVerifyEmail();

                    //Error Response Send
                    $responseData = [
                        'status'        => false,
                        'error'         => 'Your account is not verified! Please check your mail',
                    ];
                    return response()->json($responseData, 401);
                }

                $accessToken = $user->createToken(env('APP_NAME', 'trueyouclub'))->plainTextToken;

                DB::commit();

                // get closest webinar
                // $closestWebinar = Webinar::query()
                // ->select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
                // ->orderByRaw('CASE WHEN CONCAT(start_date, " ", end_time) < NOW() THEN 1 ELSE 0 END') 
                // ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')->first();

                $closestWebinar = Webinar::query()
                ->select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
                    ->where(\DB::raw('CONCAT(start_date, " ", end_time)'), '>', now())
                    ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')->first();

                $getWebinar = null;
                if ($closestWebinar) {
                    $closestWebinar->image_url = $closestWebinar->image_url ? $closestWebinar->image_url : asset(config('constants.default.no_image'));
                    $getWebinar = [
                        'title' => $closestWebinar->title ?? null,
                        'start_date' => $closestWebinar->start_date ?? null,
                        'start_time' => $closestWebinar->start_time ?? null,
                        'end_time' => $closestWebinar->end_time ?? null,
                        'image_url' => $closestWebinar->image_url ?? null,
                        'datetime' => $closestWebinar ? convertDateTimeFormat($closestWebinar->start_date.' '.$closestWebinar->start_time,'fulldatetime') .'-'. Carbon::parse($closestWebinar->end_time)->format('h:i A') : null,
                    ];
                }

                //Success Response Send
                $responseData = [
                    'status'            => true,
                    'message'           => 'You have logged in successfully!',
                    'userData'          => [
                        'id'           => $user->id,
                        'first_name'   => $user->first_name ?? '',
                        'last_name'    => $user->last_name ?? '',
                        'name'         => $user->name ?? '',
                        'profile_image'=> $user->profile_image_url ?? '',
                        'role'=> $user->roles()->first()->id ?? '',
                    ],
                    'remember_me_token' => $user->remember_token,
                    'access_token'      => $accessToken,
                    'data'=>[
                        'closest_webinar_detail' => $getWebinar,
                    ]
                ];


                $user->last_login_at = now();
                $user->save();
                
                return response()->json($responseData, 200);

            } else{

                //Error Response Send
                $responseData = [
                    'status'        => false,
                    'error'         => 'These credentials do not match our records!',
                ];
                return response()->json($responseData, 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required','email','exists:users,email,deleted_at,NULL',new ValidEmail]
        ]);

        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'errors' => $validator->errors(),
            ];
            return response()->json($responseData, 422);
        }
        
        DB::beginTransaction();
        try {
            $token = Str::random(64);
            $email_id = strtolower($request->email);
            // $user = User::where('email', $email_id)->withTrashed()->first();
            $user = User::where('email', $email_id)->first();

            /*
            if(!is_null($user->deleted_at)){
                //Error Response Send
                $responseData = [
                    'status'        => false,
                    'error'         => 'Your account has been deactivated!',
                ];
                return response()->json($responseData, 401);
            }*/

            // if(!$user->is_active){
            //     //Error Response Send
            //     $responseData = [
            //         'status'        => false,
            //         'error'         => 'Your account has been blocked!',
            //     ];
            //     return response()->json($responseData, 401);
            // }

            $userDetails = array();
            $userDetails['name'] = ucwords($user->name);

            $userDetails['reset_password_url'] = env('FRONTEND_URL').'reset-password/'.$token;

            // $userDetails['reset_password_url'] = env('FRONTEND_URL').'reset-password/'.$token.'/'.encrypt($email_id);
            
            DB::table('password_resets')->insert([
                'email'         => $email_id, 
                'token'         => $token, 
                'created_at'    => Carbon::now()
            ]);

            $subject = 'Reset Password Notification';
            Mail::to($email_id)->queue(new ResetPasswordMail($userDetails['name'],$userDetails['reset_password_url'], $subject));

            DB::commit();

            //Success Response Send
            $responseData = [
                'status'        => true,
                'message'         => __('passwords.sent'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password'                  => 'required|min:8',
            'password_confirmation'     => 'required|same:password',
            'token'                     => 'required',
        ]);

        if($validator->fails()){
            $errors = (object) $validator->errors()->messages();

            //Error Response Send
            $responseData = [
                'status'        => false,
                'errors' => $errors,
            ];
            return response()->json($responseData, 422);
        }

        DB::beginTransaction();
        try {
            $token = $request->token;
            // $email = decrypt($request->hash);

            // $updatePassword = DB::table('password_resets')->where(['email' => $email,'token' => $token])->first();

            $updatePassword = DB::table('password_resets')->where(['token' => $token])->first();

            if(!$updatePassword){
                //Error Response Send
                $responseData = [
                    'status'        => false,
                    'error'         => trans('passwords.token'),
                ];
                return response()->json($responseData, 422);

            }else{

                $user = User::where('email', $updatePassword->email)
                ->update(['password' => bcrypt($request->password)]);

                DB::table('password_resets')->where(['token' => $token])->delete();

                DB::commit();

                //Success Response Send
                $responseData = [
                    'status'  => true,
                    'message' => __('passwords.reset'),
                ];
                return response()->json($responseData, 200);

            }
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function verifyEmail($id, $hash){
        $user = User::find($id);
        
        if(!is_null($user->email_verified_at)){
            
            $responseData = [
                'status'  => true,
                'message' => 'Email is already verifed!',
            ];
            return response()->json($responseData, 200);
        }
        if ($user && $hash === sha1($user->email)) {
            $user->update(['email_verified_at' => date('Y-m-d H:i:s')]);

            //Success Response Send
            $responseData = [
                'status'  => true,
                'message' => 'Email verified successfully!',
            ];
            return response()->json($responseData, 200);
        }

        // Error Response Send
        $responseData = [
            'status'  => false,
            'error'   => 'Mail verification failed!',
        ];
        return response()->json($responseData, 401);
    }

   
    public function verifyBuyerEmailAndSetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'id'                        => 'required|numeric',
            'hash'                      => 'required',
            'password'                  => 'required|min:8',
            'password_confirmation'     => 'required|same:password',
        ]);

        if($validator->fails()){
            //Error Response Send
            $responseData = [
                'status'        => false,
                'errors' => $validator->errors(),
            ];
            return response()->json($responseData, 422);
        }

        $user = User::find($request->id);
        
        if(!is_null($user->email_verified_at)){
            
            $responseData = [
                'status'  => true,
                'message' => 'Email is already verifed!',
            ];
            return response()->json($responseData, 200);
        }

        if ($user && $request->hash === sha1($user->email)) {
            $user->update(['password' => bcrypt($request->password),'email_verified_at' => date('Y-m-d H:i:s')]);

            //Success Response Send
            $responseData = [
                'status'  => true,
                'message' => 'Email verified and password set successfully!',
            ];
            return response()->json($responseData, 200);
        }

        // Error Response Send
        $responseData = [
            'status'  => false,
            'error'   => 'Mail verification failed!',
        ];
        return response()->json($responseData, 401);
    }

    public function getEmail($userId){
        $user = User::find($userId);
        if($user ){
            if(is_null($user->email_verified_at)){
                $responseData = [
                    'status'  => true,
                    'is_verify_email' => false,
                    'message' => 'Here your email',
                    'data'    => $user->email ?? '',
                ];
                return response()->json($responseData, 200);
            } else {
                //Return Error Response
                $responseData = [
                    'status'        => false,
                    'is_verify_email' => true,
                    'error'         => trans('Email is already verifed! Please login'),
                ];
                return response()->json($responseData, 422);
            }
        }{
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 401);
        }
        
    }
}

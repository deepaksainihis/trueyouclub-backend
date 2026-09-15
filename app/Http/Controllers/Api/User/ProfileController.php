<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Webinar;
use Carbon\Carbon;


class ProfileController extends Controller
{
    public function userDetails()
    {
        $closestWebinar = Webinar::query()
        ->select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", start_time))) AS time_diff_seconds')
            ->where(\DB::raw('CONCAT(start_date, " ", start_time)'), '>', now())
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
       

        // Get the authenticated user
        $user = Auth::user();

        $user_details = [
            'first_name' => $user->first_name ? ucwords($user->first_name) : null,
            'last_name'  => $user->last_name ? ucwords($user->last_name) : null,
            'name'       => $user->name ? ucwords($user->name) :  null,
            'email'      => $user->email ?? null,
            'phone'      => $user->phone ?? null,
            'profile_image' => $user->profile_image_url ?? null,
            'is_active'  => $user->is_active ? true : false,
            'role'       => $user->roles()->select('id','title')->first()->makeHidden('pivot'),
            'closest_webinar_detail' => $getWebinar,
            'is_notification' => $user->notifications()->whereNull('read_at')->count() > 0 ? true : false,
        ];

        // Return response
        $responseData = [
            'status' => true,
            'data'   => $user_details,
        ];
        return response()->json($responseData, 200);
    }

    public function updateProfile(Request $request)
    {

        $userId = auth()->user()->id;

        $validatedData = [
            'name'  => 'required',
            'phone' => [
                'required',
                'numeric',
                'regex:/^[0-9]{7,15}$/',
                'not_in:-',
                Rule::unique('users', 'phone')->ignore($userId, 'id')
            ],
        ];


        // if(!auth()->user()->email){
        //     $validatedData['email']  = ['required', 'string', 'email', 'max:255', Rule::unique((new User)->getTable(), 'email')->ignore($userId)->whereNull('deleted_at')];
        // }


        $request->validate($validatedData, [
            'confirm_password.same' => 'The confirm password and new password must match.',
            'phone.required'=>'The phone number field is required',
            'phone.regex' =>'The phone number length must be 7 to 15 digits.',
            'phone.unique' =>'The phone number already exists.',
        ]);

        DB::beginTransaction();
        try {
            $fullName = explode(' ', $request->name);

            $updateRecords = [
                'first_name'  => $fullName[0],
                'last_name'  => isset($fullName[1]) ? $fullName[1] : null,
                'name'  => $request->name,
                'phone' => $request->phone,
            ];

            // if($request->email){
            //     $updateRecords['email'] = $request->email;
            // }

            $updatedUserRecord = User::find($userId)->update($updateRecords);

            DB::commit();

            if ($updatedUserRecord) {
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Profile has been updated',
                ];

                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());

            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function changeProfileImage(Request $request)
    {
        $userId = auth()->user()->id;

        $validatedData['profile_image'] = ['required', 'image', 'mimes:jpeg,jpg,png', 'max:' . config('constants.profile_image_size')];

        $request->validate($validatedData);

        DB::beginTransaction();
        try {

            $fetchUser = User::find($userId);

            DB::commit();
            if ($fetchUser) {
                // Start to Update Profile Image
                if ($request->hasFile('profile_image')) {

                    $actionType = 'save';
                    $uploadId = null;
                    if ($fetchUser->profileImage) {
                        $uploadId = $fetchUser->profileImage->id;
                        $actionType = 'update';
                    }
                    uploadImage($fetchUser, $request->file('profile_image'), 'user/profile-images', "profile", 'original', $actionType, $uploadId);
                }
                // End to Update Profile Image

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Profile Image Uploaded Successfully!',
                ];

                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());

            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $userId = auth()->user()->id;

        $validatedData['status'] = ['required', 'in:0,1'];

        $request->validate($validatedData);

        DB::beginTransaction();
        try {

            $fetchUser = User::where('id', $userId)->update(['is_active' => $request->status]);

            DB::commit();
            if ($fetchUser) {

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Status Updated Successfully!',
                ];

                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());

            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $userId = auth()->user()->id;

        $validatedData['old_password'] = ['required', 'string', 'min:8', new MatchOldPassword];
        $validatedData['new_password'] =  ['required', 'string', 'min:8', 'different:old_password'];
        $validatedData['confirm_password'] = ['required', 'min:8', 'same:new_password'];

        $request->validate($validatedData, [
            'confirm_password.same' => 'The confirm password and new password must match.'
        ]);

        DB::beginTransaction();
        try {
            $updateRecords['password'] = Hash::make($request->new_password);

            $updatedUserRecord = User::find($userId)->update($updateRecords);

            DB::commit();

            if ($updatedUserRecord) {
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Password Changed Successfully!',
                ];

                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());

            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Seminar;
use App\Models\User;
use App\Models\UserToken;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    public function createCheckoutSession(Request $request)
    {
       
        $rules['name'] = 'required|string';
        $rules['email'] = ['required','email:dns'];
        $rules['seminar'] = 'required|exists:seminars,id';

        $request->validate($rules);

        try {
            $request->email = strtolower($request->email);

            $seminar = Seminar::where('id',$request->seminar)->first();

            if($seminar->bookings()->count() ==  $seminar->total_ticket){

                return response()->json(['status'=>true,'error' => 'Booking Closed!'],505);

            }

            // Set your Stripe secret key
            $stripeSecretKey = getSetting('stripe_secret_key') ? getSetting('stripe_secret_key') : config('app.stripe_secret_key');
            $stripePublishableKey = getSetting('stripe_publishable_key') ? getSetting('stripe_publishable_key') : config('app.stripe_publishable_key');

            Stripe::setApiKey($stripeSecretKey);

            $authUser = User::where('email',$request->email)->first();

            //Start To Set Token
            $token = Str::random(32);
            $userEmail =  !$authUser ? $request->email :  $authUser->email;

            $userToken = UserToken::where('email', $userEmail)->first();
            if ($userToken) {
                $userToken->update([
                    'seminar_id'=> $seminar->id ?? null,
                    'token' => $token,
                    'type'  =>'checkout_token',
                ]);
            } else {
                $userToken = UserToken::create([
                    'email'   => $userEmail,
                    'seminar_id'=> $seminar->id ?? null,
                    'token'   => $token,
                    'type'    => 'checkout_token',
                ]);
            }
            //End To Set Token

            // Retrieve customer details by email
            $customers = Customer::all(['email' => $userEmail]);
            $customerStripeId = null;
            if( isset($customers->data[0]) ){
                $customer = $customers->data[0];
                $customerStripeId = $customer->id;
            }else{
                $customer = Customer::create([
                    'name'  => $request->name,
                    'email' => $request->email,
                ]);

                $customerStripeId = $customer->id;
            }

            // dd($customers->data[0]);

            //Update stripe id if user authenticate
            if( $authUser ){
                $authUser->stripe_customer_id = $customerStripeId;
                $authUser->save();
            }

            $metadata = [
                'user_name' => $request->name ?? null,
                'seminar' => json_encode([
                    'id' => $seminar->id,
                    'title' => ucwords($seminar->title),
                    'total_ticket'=> $seminar->total_ticket,
                    'ticket_price'=> $seminar->ticket_price,
                    'venue' => $seminar->venue,
                    'start_date' => $seminar->start_date,
                    'start_time' => $seminar->start_time,
                    'end_time'   => $seminar->end_time,
                ]),
            ];

    
            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => config('constants.default_currency'),
                            'product_data' => [
                                'name' => ucwords($seminar->title),
                            ],
                            'unit_amount' => (float)$seminar->ticket_price * 100, // Amount in cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . 'seminar-ticket/'.encrypt($userEmail).'/'.$token, 
                'cancel_url' => env('FRONTEND_URL') . 'paymentfailed',    
                'metadata' => $metadata,
            ];
           
            if ($customerStripeId) {
                $sessionData['customer'] = $customerStripeId;
            }

            // Create a Checkout Sessions
            $session = StripeSession::create($sessionData);

            return response()->json(['status'=>true,'session' => $session]);
        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            return response()->json(['status'=>false,'error' => trans('messages.error_message'),'error_detail'=>$e->getMessage().'->'.$e->getLine()], 500);
        }
    }

    public function checkoutSuccess(Request $request)
    {

        $requestVal['email'] = decrypt($request->email);
        $requestVal['token'] = $request->token;

       
        $rules['email'] = 'required|string|email';
        $rules['token'] = 'required';

        // $request->validate($rules);
     
        $validator = Validator::make($requestVal, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $requestEmail = $requestVal['email'];
        $sessionToken = $requestVal['token'];

        $userToken = UserToken::where('email', $requestEmail)->where('token', $sessionToken)->first();

        if ($userToken) {
            
            $bookingDetails = Booking::where('email', $requestEmail)->where('bookingable_id',$userToken->seminar_id)->orderBy('id','desc')->first();
            
            $ticketDetails = null;
            if($bookingDetails){
                $ticketDetails = [
                    'name' => ucwords($bookingDetails->name),
                    'email' => $bookingDetails->email,
                    'title' => ucwords($bookingDetails->seminar->title),
                    'booking_number' => $bookingDetails->booking_number,
                    'date' => convertDateTimeFormat($bookingDetails->seminar->start_date,'fulldate'),
                    'start_time' => \Carbon\Carbon::parse($bookingDetails->seminar->start_time)->format('h:i A'),
                    'end_time' => \Carbon\Carbon::parse($bookingDetails->seminar->end_time)->format('h:i A'),
    
                    'venue' => $bookingDetails->seminar->venue,
                    'ticket_price' => number_format($bookingDetails->seminar->ticket_price,2),
                    'seminar_image' => $bookingDetails->seminar->image_url ? $bookingDetails->seminar->image_url : asset(config('constants.default.no_image'))
                ];
            }
           

            $userToken->seminar_id = null;
            $userToken->token = null;
            $userToken->save();
            
            $response = [
                'status'  => true,
                'message' => 'Payment Successfully!',
                'data'    => $ticketDetails,
            ];

            return response()->json($response, 200);
        } else {
            // The request is not from the same session.
            return response()->json(['status' => false, 'message'=>'Expired Token!'], 200);
        }
    }


  
  
}

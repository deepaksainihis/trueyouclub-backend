<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;

class TwilioController extends Controller
{
    public function sendSms(Request $request)
    {
        $userId = auth()->user()->id;
        $rules['phone'] = ['required', 'numeric','digits:10','not_in:-','unique:users,phone,'. $userId.',id,deleted_at,NULL'];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $twilioPhoneNumber = config('services.twilio.from');
            
            $otpNumber = generateOTP();
          
            /*$isOtpSend = User::where('id',$userId)->first();
            $isOtpSend->phone = $request->phone;
            $isOtpSend->otp = $request->otpNumber;
            $isOtpSend->save();
            $isOtpSend->buyerDetail()->update(['phone'=>$request->phone]);*/

            $isOtpSend = User::where('id',$userId)->update(['phone'=>$request->phone,'otp'=>$otpNumber]);
            if($isOtpSend){
                $client = new Client($sid, $token);

                $toPhoneNumber = config('constants.twilio_country_code').$request->phone;
                $message = 'Your verification code is: '.$otpNumber.'. Please keep this code confidential and do not share it with anyone.';

                $client->messages->create(
                    $toPhoneNumber,
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => $message
                    ]
                );

                DB::commit();
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => trans('messages.auth.verification.otp_send_success'),
                ];
                return response()->json($responseData, 200);
            }
        }catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }
}
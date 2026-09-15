<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\User;
use App\Models\UserToken;
use App\Models\Booking;
use App\Models\Seminar;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchasedSeminarTicketMail;

class StripeWebhookController extends Controller
{
    
    public function handleStripeWebhook(Request $request)
    {
        Log::info('Start stripe webhook');

        $stripeSecretKey = getSetting('stripe_secret_key') ? getSetting('stripe_secret_key') : config('app.stripe_secret_key');
        // $stripePublishableKey = getSetting('stripe_publishable_key') ? getSetting('stripe_publishable_key') : config('app.stripe_publishable_key');

        Stripe::setApiKey($stripeSecretKey);
        
        $payload = $request->getContent();
        $stripeSignatureHeader = $request->header('Stripe-Signature');

        $endpointSecret = getSetting('stripe_webhook_secret_key') ? getSetting('stripe_webhook_secret_key') : env('STRIPE_WEBHOOK_SECRET_KEY'); // Replace with the actual signing secret

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $stripeSignatureHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::info('Invalid payload!');
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::info('Invalid signature!');
            $data = [
                'error_message' => $e->getMessage() . '->' . $e->getLine()
            ];
            return response()->json(['error' => 'Invalid signature', 'data' => $data], 400);
        }

        try {
            // Handle the event based on its type
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                break;

                case 'checkout.session.async_payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                break;

                default:
                    Log::info('Invalid Event fired!');
            }
        } catch (\Exception $e) {

            // dd($e->getMessage().'->'.$e->getLine());
            return response()->json(['error' => $e->getMessage() . '->' . $e->getLine()], 500);
            // return response()->json(['error' => 'Something went wrong!'], 400);
        }

        Log::info('End stripe webhook');

        return response()->json(['success' => true]);
    }


    private function handleCheckoutSessionCompleted($eventDataObject)
    {
        Log::info('Start stripe webhook of checkout session completed');
        
        $customer_stripe_id = $eventDataObject->customer;

        if (isset($eventDataObject->metadata->seminar)) {
            $customer = User::where('stripe_customer_id', $customer_stripe_id)->first();
            $customerDetails = $eventDataObject->customer_details;

            $customerName = $eventDataObject->customer_details->name;
            $customerEmail = $eventDataObject->customer_details->email;

            $transaction = Transaction::where('payment_intent_id', $eventDataObject->payment_intent)->where('status','success')->exists();
            if (!$transaction) {

                $userName = $eventDataObject->metadata->user_name ? ucwords($eventDataObject->metadata->user_name) : $customerName;
                $seminarObj = json_decode($eventDataObject->metadata->seminar);

                // Save data to transactions table
                Transaction::create([
                    'user_id' => $customer ? $customer->id : null,
                    // 'name'  => $customerDetails->name ?? null,
                    'name'  => $userName ?? null,
                    'email' => $customerDetails->email ?? null,
                    'user_json' => $customerDetails,
                    'ticket_id' => $seminarObj->id,
                    'ticket_json' => json_decode($eventDataObject->metadata->seminar,true),
                    'type' =>'seminar',
                    'description'=>'Seminar Ticket Purchased',
                    'payment_intent_id' => $eventDataObject->payment_intent,
                    'amount' => (float)$eventDataObject->amount_total / 100,
                    'currency' => $eventDataObject->currency,
                    'payment_method' => $eventDataObject->payment_method_types[0],
                    'payment_type'   => 'credit',
                    'status' => 'success',
                    'payment_json' => $eventDataObject,
                ]);

                //Make entry in booking table
                $bookingNumber = generateBookingNumber($seminarObj->id);

                $seminarBooking = Seminar::find($seminarObj->id);
                $booking = $seminarBooking->bookings()->create([
                    'user_id' => $customer ? $customer->id : null,
                    // 'name'  => $customerDetails->name ?? null,
                    'name'  => $userName ?? null,
                    'email' => $customerDetails->email ?? null,
                    'user_details' =>  $customerDetails,
                    'bookingable_details'=> json_decode($eventDataObject->metadata->seminar,true),
                    'booking_number' => $bookingNumber, 
                    'type'=>'seminar',
                ]);


                $subject = 'Seminar Ticket Purchased';
                Mail::to($customerEmail)->queue(new PurchasedSeminarTicketMail($subject, $userName, $customerEmail,$seminarBooking,$bookingNumber));
    
                if($customer){
                    $notification_message = config('constants.seminar_booked_notification_message');
                    $notification_message .=  ' 📆 Event Details: '.
                                              ' '.$seminarBooking->title. 
                                              ' 🅱️ Booking Number : '.$bookingNumber.
                                              ' 📆 DateTime : '.convertDateTimeFormat($seminarBooking->start_date.' '.$seminarBooking->start_time,'fulldatetime') .' - '.Carbon::parse($seminarBooking->end_time)->format('h:i A').
                                              ' 📍 Venue : '.ucwords($seminarBooking->venue);

                    Notification::send($customer, new UserNotification($customer, $notification_message));    
                }
              

            }
        }
        
    }


    private function handlePaymentFailed($eventDataObject)
    {
        Log::info('Start stripe webhook of payment failed');
       
        $customer_stripe_id = $eventDataObject->customer;

        if (isset($eventDataObject->metadata->seminar)) {
            $customer = User::where('stripe_customer_id', $customer_stripe_id)->first();
            $customerDetails = $eventDataObject->customer_details;

            $customerName = $eventDataObject->customer_details->name;
            $customerEmail = $eventDataObject->customer_details->email;

            $userName = $eventDataObject->metadata->user_name ? ucwords($eventDataObject->metadata->user_name) : $customerName;

            $seminarObj = json_decode($eventDataObject->metadata->seminar);

            $transaction = Transaction::where('payment_intent_id', $eventDataObject->payment_intent)->where('status','failed')->exists();
            if (!$transaction) {
               
                // Save data to transactions table
                Transaction::create([
                    'user_id' => $customer ? $customer->id : null,
                    // 'name'  => $customerDetails->name ?? null,
                    'name'  => $userName ?? null,
                    'email' => $customerDetails->email ?? null,
                    'user_json' => $customerDetails,
                    'ticket_id' => $seminarObj->id,
                    'ticket_json' => json_decode($eventDataObject->metadata->seminar,true),
                    'type' =>'seminar',
                    'description'=>'Seminar Ticket Purchased',
                    'payment_intent_id' => $eventDataObject->payment_intent,
                    'amount' => (float)$eventDataObject->amount_total / 100,
                    'currency' => $eventDataObject->currency,
                    'payment_method' => $eventDataObject->payment_method_types[0],
                    'payment_type'   => 'credit',
                    'status' => 'failed',
                    'payment_json' => $eventDataObject,
                ]);

            }
        }

    }

}

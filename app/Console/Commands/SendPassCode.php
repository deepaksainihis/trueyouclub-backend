<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Seminar;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Mail\SendPassCodeMail;
use Illuminate\Support\Facades\Mail;

class SendPassCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-passcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Passcode should be send to all guest user after seminar ends for register';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            \Log::info("Start send passcode command!");

            $seminars = Seminar::whereRaw('CONCAT(start_date, " ", end_time) <= NOW()')->where('status',1)->where('cron_status',0)->get();

            foreach($seminars as $seminar){
                
                //Update Seminar Status
                Seminar::where('id',$seminar->id)->update(['cron_status'=>1]);

                //Retrieve all seminar bookings
                $bookings = Booking::where('bookingable_id',$seminar->id)->where('status',0)->get();

                foreach($bookings as $booking){

                    $checkUser = User::where('email',$booking->email)->first();
                    if(!$checkUser){
                        $generateRandom = Str::random(8);
                        
                        $passcode = $booking->booking_number.strtoupper($generateRandom);

                        Booking::where('id',$booking->id)->update(['passcode'=> $passcode]);
                    
                        //Send welcome mail for user
                        $subject = 'Welcome to ' . config('app.name').'! Complete your registration with the “Golden Gateway Code”';
                        Mail::to($booking->email)->queue(new SendPassCodeMail($subject, $booking->name, $passcode));
                    }
                }
               
            }

            \Log::info("End send passcode command!");

            return true;
        }catch (Exception $e) {
            return $e;
        }
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Seminar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; 

class SeminarController extends Controller
{
    public function index()
    {

        try {
           
            $getAllRecords = Seminar::select('id', 'title', 'total_ticket', 'ticket_price', 'start_date', 'start_time', 'end_time', 'venue')
            ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
            ->where(\DB::raw('CONCAT(start_date, " ", end_time)'), '>', now())
            ->where('status',1)
            ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')
            ->paginate(10);
            
            if ($getAllRecords->count() > 0) {

                foreach($getAllRecords as $record){
                    $record->datetime = convertDateTimeFormat($record->start_date.' '.$record->start_time,'fulldatetime') .'-'. Carbon::parse($record->end_time)->format('h:i A');

                    $record->imageUrl = $record->image_url ? $record->image_url : asset(config('constants.default.no_image'));

                    $record->remain_ticket = (int)$record->total_ticket - (int)$record->bookings()->where('type','seminar')->count();

                    $seminarStartDateTime = Carbon::parse($record->start_date . ' ' . $record->start_time);

                    $record->isBookingClosed = (((int)$record->remain_ticket == 0) || (now() >= $seminarStartDateTime) ) ? true : false;

                }

                $responseData = [
                    'status'  => true,
                    'data'    => $getAllRecords,
                ];
                return response()->json($responseData, 200);
            } else {
                $responseData = [
                    'status'  => false,
                    'data'    => $getAllRecords,
                    'message' => 'No Record Found',
                ];
                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }
}

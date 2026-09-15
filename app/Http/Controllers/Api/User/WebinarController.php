<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WebinarController extends Controller
{
    public function index()
    {

        try {
            
            // $getAllRecords = Webinar::select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
            // ->orderByRaw('CASE WHEN CONCAT(start_date, " ", end_time) < NOW() THEN 1 ELSE 0 END') 
            // ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')
            // ->paginate(10);

            $getAllRecords = Webinar::select('*')
            ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
            ->orderByRaw('
                CASE 
                    WHEN CONCAT(start_date, " ", end_time) <= NOW() AND CONCAT(start_date, " ", end_time) >= NOW() THEN 0
                    WHEN CONCAT(start_date, " ", end_time) > NOW() THEN 1
                    ELSE 2
                END ASC,
                time_diff_seconds > 0 ASC, ABS(time_diff_seconds) ASC
            ')
            ->paginate(10);

            if ($getAllRecords->count() > 0) {

                foreach($getAllRecords as $record){
                    $record->image_url = $record->image_url ? $record->image_url : asset(config('constants.default.no_image'));
                    $record->datetime = convertDateTimeFormat($record->start_date.' '.$record->start_time,'fulldatetime') .'-'. Carbon::parse($record->end_time)->format('h:i A');
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

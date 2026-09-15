<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRequest;
use App\Models\VolunteerAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VolunteerController extends Controller
{
    public function volunteerAvailability(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'year'  => 'required',
        ]);

        try {
            
            $month = $request->month;
            $year  = $request->year;

            $records = VolunteerAvailability::select('id','volunteer_id','date','start_time','end_time')->where('volunteer_id',auth()->user()->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

            $withEventRequests = [];
            $withoutEventRequests = [];
            
            foreach($records as $index => $record){
                 $eventRequest = EventRequest::where('volunteer_id', $record->volunteer_id)
                ->where(function($query) use($record) {
                    $query->where('status', 1)
                        ->whereHas('event', function($query) use($record) {
                            $query->where(function($q) use($record) {
                                $q->where('event_date', '=', $record->date)
                                ->where('start_time', '=', $record->start_time);
                            });
                        });
                })->first();

                $record->event_id = $eventRequest ? $eventRequest->event_id : null;
                $record->event_status = $eventRequest ? $eventRequest->status : null;

                $record->start_time = Carbon::parse($record->start_time)->format('h A');
                $record->end_time = Carbon::parse($record->end_time)->format('h A');


                $record->title = 'Availability '.($index+1).' ('.$record->start_time.' - '.$record->end_time.')';
                if($eventRequest){
                    $record->title = $eventRequest->event ?  $eventRequest->event->title.' ('.$record->start_time.' - '.$record->end_time.')' : 'Availability '.($index+1).' ('.$record->start_time.' - '.$record->end_time.')';
                    
                     $withEventRequests[] = $record;
                     
                }else{
                    
                     $withoutEventRequests[] = $record;
                }

            }
            
            $sortedRecords = collect($withEventRequests)->merge($withoutEventRequests)
            ->sortBy('date')
            ->values(); 

            $responseData = [
                'status'  => true,
                'data'    => $sortedRecords,
            ];                 
            return response()->json($responseData, 200);
         
        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());

            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine()
            ];
            return response()->json($responseData, 500);
        }
    }

    public function storeVolunteerAvailability(Request $request)
    {
        /*
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time_slots' => ['required', 'array', 'min:1'],
            'time_slots.*.start_time' => ['required', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] ?(AM|PM)$/i'],
            'time_slots.*.end_time' => ['required', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] ?(AM|PM)$/i', function ($attribute, $value, $fail) use ($request) {
                $index = explode('.', $attribute)[1];
                $start_time = $request->input("time_slots.$index.start_time");        
                // Convert times to 24-hour format using Carbon
                try {
                    $startTime24 = Carbon::parse($start_time)->format('H:i');
                    $endTime24 = Carbon::parse($value)->format('H:i');
        
                    // Check if end_time is after start_time
                    if ($endTime24 <= $startTime24) {
                        $fail('The end time must be after the start time.');
                    }
                } catch (\Exception $e) {
                    $fail('Invalid time format.');
                }
            }],
        ],[],$this->getCustomAttributes($request->input('time_slots')));
        */     
       
        $request->validate([
           'start_date' => 'required|date|before_or_equal:end_date',
           'end_date'   => 'required|date|after_or_equal:start_date',
           'time_slots' => ['required', 'array', 'min:1'],
        ]);

        // Custom validation to ensure format and logic for end_time after start_time
        $timeSlots = $request->input('time_slots');
        $errors = [];
        $timeFormatRegex = "/^(0?[1-9]|1[0-2]):[0-5][0-9] ?(AM|PM)$/i";
        // Iterate through time slots and validate


        foreach ($timeSlots as $index => $slot) {
            $start_time = $slot['start_time'] ?? null;
            $end_time = $slot['end_time'] ?? null;

            // Validate time format using regex
            if (!preg_match($timeFormatRegex, $start_time) || !preg_match($timeFormatRegex, $end_time)) {
                $errors[] = 'Invalid time format. Ensure times are in the format HH:MM AM/PM.';
                continue; // Skip to the next slot
            }

            // Check if required fields are present
            if (!$start_time || !$end_time) {
                $errors[] = 'Both start time and end time are required for all time slots.';
                continue; // Skip to the next slot
            }

            // Convert times to 24-hour format for comparison
            try {
                $startTime24 = Carbon::createFromFormat('g:i A', $start_time)->format('H:i');
                $endTime24 = Carbon::createFromFormat('g:i A', $end_time)->format('H:i');
                // Custom validation checks
                if ($endTime24 <= $startTime24) {
                    $errors[] = 'The end time must be a time after the start time.';
                }
                
            } catch (\Exception $e) {
                $errors[] = 'Invalid time format.';
            }
        }
        
        if ($errors) {
            return response()->json([
                'message' => 'The end time must be after the start time.',
                'errors' => [
                    'time_slots' => array_values(array_unique($errors)) // Ensure each error message is unique
                ]
            ], 422);
        }        
        

        try{
            DB::beginTransaction();
            
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $dates = collect();

            while ($startDate->lte($endDate)) {
                $dates->push($startDate->copy()->toDateString());
                $startDate->addDay(); 
            }

            $allSelectedDates = $dates->unique()->values();

            foreach($allSelectedDates as $selectedDate){
                $date = Carbon::parse($selectedDate)->format('Y-m-d');
                $time_slots = $request->time_slots;
                foreach($time_slots as $slot){
                    $startTime24 = Carbon::parse($slot['start_time'])->format('H:i');
                    $endTime24 = Carbon::parse($slot['end_time'])->format('H:i');
    
                    $existingSlot = VolunteerAvailability::where('date', $date)
                    ->where('start_time', $startTime24)
                    ->where('end_time', $endTime24)
                    ->first();
            
                    if ($existingSlot) {
                        continue;
                    }
                    
                    $availablityData = [
                        'date'       => $date,
                        'start_time' => $startTime24,
                        'end_time'   => $endTime24,
                    ];
    
                    VolunteerAvailability::create($availablityData);
    
                }
            }

            DB::commit();
            
            $responseData = [
                'status'  => true,
                'message' => 'Availablity added successfully!'
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
    
            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine()
            ];
            return response()->json($responseData, 500);
        }
    }


    public function getCustomAttributes($timeSlots)
    {
        $customAttributes = [];
        foreach ($timeSlots as $index => $timeSlot) {
            $slotNumber = $index + 1;
            $customAttributes["time_slots.$index.start_time"] = "start_time_$slotNumber";
            $customAttributes["time_slots.$index.end_time"] = "end_time_$slotNumber";
        }
        
        return $customAttributes;
    }

    public function destroyVolunteerAvailability($id){
        try{
            DB::beginTransaction();
            $model = VolunteerAvailability::find($id);
            if($model){
                $model->delete();

                DB::commit();
                $responseData = [
                    'status'  => true,
                    'message' => 'Availablity deleted successfully!'
                ];
                return response()->json($responseData, 200);
            }

            $responseData = [
                'status'  => false,
                'message' => trans('messages.no_record_found')
            ];
            return response()->json($responseData, 404);

        } catch (\Exception $e) {
            DB::rollBack();

            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine()
            ];
            return response()->json($responseData, 500);
        }
    }

    

    public function eventDetails(Request $request)
    {
        $request->validate([
            'event_id' => ['required','exists:events,id,deleted_at,NULL'],
        ]);

        try
        {         
            $event = Event::select('id', 'title', 'description', 'event_date', 'start_time', 'end_time', 'location_id','status','created_by')->where('id',$request->event_id)
            ->first();
    
            $event->title       = ucwords($event->title);

            $formattedEventDate = convertDateTimeFormat($event->event_date, 'fulldate');
            $event->formatted_event_date = $formattedEventDate;

            $event->start_time  = convertDateTimeFormat($event->start_time, 'fulltime');
            $event->end_time  = convertDateTimeFormat($event->end_time, 'fulltime');
            $event->image_url   = $event->featured_image_url ? $event->featured_image_url : asset(config('constants.default.no_image')); 
            $event->created_by  = $event->user->name ?? null;    

            $event->makeHidden(['user', 'featuredImage','event_date']);   
                    
            $responseData = [
                'status'  => true,
                'data' => $event,
            ];                 
            return response()->json($responseData, 200);
           
        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }

}

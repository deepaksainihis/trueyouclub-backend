<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\EventRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VolunteerAvailability;


class EventRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();        

        try
        {
            $getAllRecords = EventRequest::with(['event' => function($query) {
                $query->select(['id', 'title', 'slug', 'description', 'event_date', 'start_time','end_time'])
                      ->where(function($query) {
                          $query->where('event_date', '>', now()->toDateString());
                                // ->orWhere(function($query) {
                                //     $query->where('event_date', '=', now()->toDateString())
                                //           ->where('start_time', '>', now()->toTimeString());
                                // });
                      });
            }])->select('id', 'event_id', 'volunteer_id', 'custom_message', 'status', 'created_at', 'created_by')
                ->where('status',0)
                ->where('volunteer_id', $user->id)
                ->whereHas('event', function($query) {
                    $query->where(function($query) {
                        $query->where('event_date', '>', now()->toDateString());
                            //   ->orWhere(function($query) {
                            //       $query->where('event_date', '=', now()->toDateString())
                            //             ->where('start_time', '>', now()->toTimeString());
                            //   });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            
            if ($getAllRecords->count() > 0){  
                foreach ($getAllRecords as $key=>$record)
                {        
                    $record->formatted_date  = convertDateTimeFormat($record->created_at, 'fulldate');                            
                    $record->time            = $record->created_at->format('H:i A');                               
                    $record->created_by      = $record->user->name ?? null;                    
                    $record->makeHidden(['user']);

                    if ($record->event) {
                       
                        $formattedEventDate = convertDateTimeFormat($record->event->event_date, 'fulldate');
                        $record->event->formatted_event_date = $formattedEventDate;
                        $record->event->start_time  = \Carbon\Carbon::parse($record->event->start_time)->format('h:i A');
                        $record->event->end_time  = \Carbon\Carbon::parse($record->event->end_time)->format('h:i A');
                        $record->event->image_url   = $record->event->featured_image_url ? $record->event->featured_image_url : asset(config('constants.default.no_image')); 
                        $record->event->created_by  = $record->user->name ?? null;    

                        $record->event->makeHidden(['user', 'featuredImage']);       
                    }
                }                    
                 
                $responseData = [
                    'status'  => true,
                    'data' => $getAllRecords,
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
                'error_details' => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function updateStatus(Request $request)
    {     
        try{            
            $volunteer_id = Auth::user()->id;
            $request->validate([
                'id' => 'required|numeric|exists:event_requests,id',
                'status' => 'required|in:'.implode(',',array_keys(config('constants.event_request_status')))
            ]);

            $eventRequest = EventRequest::with('event')->where('id', $request->id)
            ->where('volunteer_id', $volunteer_id)
            ->first();

            $event = $eventRequest->event;
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

           // Check if the volunteer is available for the event time
            $available = VolunteerAvailability::where('volunteer_id', $volunteer_id)
                ->where('date', $event->event_date)
                ->where('start_time', '<=', $event->start_time)
                ->where('end_time', '>=', $event->end_time)
                ->exists();
            
            // Check if the volunteer has already accepted another event within the same timing
            $conflictingEvent = EventRequest::where('volunteer_id', $volunteer_id)
                ->where('status', 1) // Already accepted
                ->whereHas('event', function($query) use ($event) {
                    $query->where('event_date', $event->event_date)
                        ->where(function($q) use ($event) {
                            $q->whereBetween('start_time', [$event->start_time, $event->end_time])
                              ->orWhereBetween('end_time', [$event->start_time, $event->end_time]);
                        });
                })
                ->exists();
            
            if (($available && !$conflictingEvent) || $request->status == 2) {
                EventRequest::where('id',$request->id)->where('volunteer_id',$volunteer_id)->update(['status'=> $request->status]);

                $message = 'Reponse Submitted!';
                if($request->status == 1){
                    $message = trans('messages.event.accepted');
                }else if($request->status == 2){
                    $message = trans('messages.event.declined');
                }
    
                $responseData = [
                    'status'  => true,
                    'message' => $message 
                ];
                return response()->json($responseData, 200);
            }

            $responseData = [
                'status'  => false,
                'error'   => 'You are not available or is already booked for another event during the requested time.',
            ];
            return response()->json($responseData, 409);

        }catch(\Exception $e){
            // dd($e->getMessage().'->'.$e->getLine());
            $responseData = [
                'status'  => false,
                'error'   => trans('messages.error_message'),
                'error_details' => $e->getMessage().'->'.$e->getLine()
            ];
            return response()->json($responseData, 500);
        }
    }

    public function eventHistory()
    {
        $user = Auth::user();        

        try
        {         
            $getAllRecords = EventRequest::select('id', 'event_id', 'volunteer_id', 'custom_message', 'status', 'created_at', 'created_by')
            ->where('volunteer_id', $user->id)
            ->where(function($query) {
                $query->where('status', 1)
                    ->orWhere('status', 2)
                    ->orWhereHas('event', function($query) {
                        $query->where(function($query) {
                            $query->where('event_date', '<', now()->toDateString())
                                    ->orWhere(function($query) {
                                        $query->where('event_date', '=', now()->toDateString())
                                            ->where('start_time', '<', now()->toTimeString());
                                    });
                        });
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    
            
            if ($getAllRecords->count() > 0){  
                foreach ($getAllRecords as $key=>$record)
                {        
                    $record->formatted_date  = convertDateTimeFormat($record->created_at, 'fulldate');                            
                    $record->time            = $record->created_at->format('H:i A');                               
                    $record->created_by      = $record->user->name ?? null;                    
                    $record->makeHidden(['user']);

                    if ($record->event) {
                        $formattedEventDate = convertDateTimeFormat($record->event->event_date, 'fulldate');
                        $record->event->formatted_event_date = $formattedEventDate;
                        $record->event->start_time  = convertDateTimeFormat($record->event->start_time, 'fulltime');  
                        $record->event->image_url   = $record->event->featured_image_url ? $record->event->featured_image_url : asset(config('constants.default.no_image')); 
                        $record->event->created_by  = $record->user->name ?? null;    

                        $record->event->makeHidden(['user', 'featuredImage']);       
                    }
                }                    
                 
                $responseData = [
                    'status'  => true,
                    'data' => $getAllRecords,
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
                'error_details' => $e->getMessage().'->'.$e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }
}

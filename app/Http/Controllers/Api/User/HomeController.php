<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Quote;
use Carbon\Carbon;

class HomeController extends Controller
{

    public function index()
    {
        try {
            $data = [];
            $user = Auth::user();
            $today = Carbon::today();
            $todayQuote = Quote::whereDate('created_at', $today)->first();

            // Start: Task Tracking
                // $userQuoteCount = $user->quotes()->withTrashed()->count();
                $userQuoteCount = $user->quotes()->count();

                if($userQuoteCount >= 63){ // 63 days/tasks = 9 weeks
                    $userQuoteCount = $userQuoteCount%63;
                }
                
                $remainingValue = $userQuoteCount%7;  // Total Tasks % 7  = Total Completed/Skipped Task Within Last 7 Days 
                $completedWeeks = floor($userQuoteCount/7); // Total Tasks / 7 = Total completed weeks

                $daysData = [1=>null, 2=>null, 3=>null, 4=>null, 5=>null, 6=>null, 7=>null];
                // $daysData = [];
                if($remainingValue > 0){
                    // get within last 7 days Task details
                    $uqdata = $user->quotes()->withPivot('status', 'created_at')->orderBy('created_at', 'desc')->limit($remainingValue)
                    // ->withTrashed()
                    ->get()->pluck('pivot');
                    foreach($uqdata as $key => $pivotData){
                        $daysData[($remainingValue-$key)] = $pivotData->status == "completed" ? true : false;
                    }
                }

                if(($remainingValue == 0) && $userQuoteCount > 0){
                    $daysData[7] = true;
                }

                $data['task_tracking_data']['week_days'] = $daysData;
                $data['task_tracking_data']['completed_week'] = $completedWeeks;
            // End: Task Tracking

            // Start: Quote of the day
                if (!$todayQuote) {
                    $data['today_qoute_data']['is_completed'] = false;
                    $data['today_qoute_data']['percentage'] = 0;
                    $data['today_qoute_data']['message'] = null;
                } else {
                    $data['today_qoute_data']['is_completed'] = false;
                    
                    if($todayQuote->users()->where('user_id',$user->id)->count() > 0){
                        $data['today_qoute_data']['is_completed'] = true;
                    }

                    $totalUsers = getTotalUsers();
                    $submissionPercentage = ($todayQuote->users()->count() / (int)$totalUsers) * 100;
                    $quoteMessage = $todayQuote->message;

                    $data['today_qoute_data']['id'] = $todayQuote->id;
                    $data['today_qoute_data']['percentage'] = round($submissionPercentage);
                    $data['today_qoute_data']['message'] = $quoteMessage;
                }
            // End: Quote of the day

            $data['is_break'] = $user->is_active ? true : false;

            $responseData = [
                'status' => true,
                'data' => $data
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage() . '->' . $e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message').$e->getMessage() . '->' . $e->getLine(),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'quote_id'    => 'required|numeric',
        ], [
            'quote_id.required'   => 'This field is required',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $todayQuote = Quote::find($request->quote_id);
            if (!$todayQuote) {
                $responseData = [
                    'status'        => false,
                    'message'       => "No quote exist in record",
                ];
                return response()->json($responseData, 404);
            } else {
                $todayQuoteData = $todayQuote->created_at->format('Y-m-d');
                $today = Carbon::today()->format('Y-m-d');

                if ($todayQuoteData == $today) {
                    if(!$user->is_active){
                        $user->quotes()->attach($todayQuote, ['created_at' => now(), 'status' => 'completed']);
    
                        // $quoteTasksCount = $user->quotes()->withTrashed()->count();
                        $quoteTasksCount = $user->quotes()->count();
                        $countForStar = config('constants.user_star_no_with_task_count');

                        if(!$user->vip_at){
                            if($quoteTasksCount == $countForStar['1_star']){
                                $user->update(['star_no' => 1]);
                            } else if($quoteTasksCount > $countForStar['1_star'] && $quoteTasksCount == $countForStar['2_star']){
                                $user->update(['star_no' => 2]);
                            } else if($quoteTasksCount > $countForStar['2_star'] && $quoteTasksCount == $countForStar['3_star']){
                                $user->update(['star_no' => 3]);
                            } else if($quoteTasksCount > $countForStar['3_star'] && $quoteTasksCount == $countForStar['4_star']){
                                $user->update(['star_no' => 4]);
                            } else if($quoteTasksCount > $countForStar['4_star'] && $quoteTasksCount == $countForStar['5_star']){
                                $user->update(['star_no' => 5, 'vip_at' => now()->format('Y-m-d H:i:s')]);
                                cacheVipUsers('set');
                            }
                        }
                    }
                    DB::commit();
                    $responseData = [
                        'status'        => true,
                        'message'       => "Completed Today Task Successfully!",
                    ];
                    return response()->json($responseData, 200);
                } else {
                    $responseData = [
                        'status'        => false,
                        'message'       => "Invalid quote for today",
                    ];
                    return response()->json($responseData, 404);
                }
            }
        } catch (\Exception $e) { 
            DB::rollBack();
            // dd($e->getMessage() . '->' . $e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }


    public function userNotifications(Request $request){
        try {
            $user = Auth::user();
            
            $user->notifications()->update(['read_at'=>Carbon::now()]);

            $notifications = $user->notifications()->select('id', 'data', 'created_at', 'read_at')->latest()->paginate(10);

            if ($notifications->count() > 0) {
                foreach ($notifications as $key=>$record) {
                    $now = Carbon::now();
                    $notificationDate = Carbon::parse($record->created_at)->diffForHumans($now);
                    $notificationDate = str_replace('before', 'ago', $notificationDate);

                    $record->notification_date = $notificationDate;

                    $notificationData = $record->data;
                    $record->notification_message = $notificationData['message'];
                }
                $responseData = [
                    'status'  => true,
                    'data'    => $notifications,
                ];
                return response()->json($responseData, 200);
            } else {
                $responseData = [
                    'status'  => true,
                    'data' => null,
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

    public function deleteNotification(Request $request, $id){
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $notification = $user->notifications()->where('id', $id)->first();
            if($notification){
                $notification->delete();
                DB::commit();

                $responseData = [
                    'status'        => true,
                    'message'       => "Notification deleted successfully",
                    'data'          => null,
                ];

                $getNotifications = $user->notifications()->select('id', 'data', 'created_at', 'read_at')->latest()->paginate(10);

                if ($getNotifications->count() > 0) {
                    foreach ($getNotifications as $key=>$record) {
                        $now = Carbon::now();
                        $notificationDate = Carbon::parse($record->created_at)->diffForHumans($now);
                        $notificationDate = str_replace('before', 'ago', $notificationDate);
    
                        $record->notification_date = $notificationDate;
    
                        $notificationData = $record->data;
                        $record->notification_message = $notificationData['message'];
                    }

                    $responseData['data'] = $getNotifications;
                }

               
                return response()->json($responseData, 200);
            } else {
                $responseData = [
                    'status'        => false,
                    'message'       => "No data found",
                ];
                return response()->json($responseData, 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage() . '->' . $e->getLine());
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }

    public function getReward(Request $request){
        try {
            $currentUser = Auth::user();
            
            $data = [ 'star' => $currentUser->star_no];
            
            $vipUsers = cacheVipUsers();
            $data['vip_users'] = $vipUsers;
             
            $responseData = [
                'status'  => true,
                'data'    => $data,
            ];
            return response()->json($responseData, 200);
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

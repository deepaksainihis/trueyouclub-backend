<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use App\Models\Seminar;
use Illuminate\Support\Carbon;

class CommanController extends Controller
{
   
    public function siteSettingDetails()
    {
        $setting_details = [
            'favicon'     =>  getSetting('favicon'),
            'site_logo'   =>  getSetting('site_logo'),
            'footer_logo' =>  getSetting('footer_logo'),
            'short_logo'  =>  getSetting('short_logo'),
            'support'     =>  [
                'email' => getSetting('support_email'),
                'phone' => getSetting('support_phone'),
                'location' => getSetting('support_location'),
            ],
            'social_media' => [
                'youtube'   => getSetting('youtube'),
                'instagram' => getSetting('instagram'),
                'linkedin'  => getSetting('linkedin'),
                'facebook'  => getSetting('facebook'),
            ],
        ];
        // Return response
        $responseData = [
            'status' => true,
            'data'   => $setting_details,
        ];
        return response()->json($responseData, 200);
    }

    public function getLatestRecords($pageName){
        try {
            $currentDate = now()->format('Y-m-d');
            $currentTime = now()->format('H:i');

            $currentDateTime = Carbon::now();

            $latestRecords = [];
            if($pageName == 'login'){

                // Start Latest News Details
                $latestNews = Post::where('type','news')->where('publish_date','<=',$currentDate)->orderBy('publish_date', 'desc')->limit(2)->get();

                foreach($latestNews as $key=>$news){
                    $latestRecords['news'][$key]['title']   = $news->title;
                    $latestRecords['news'][$key]['slug']    = $news->slug;
                    $latestRecords['news'][$key]['content'] = $news->content;
                    $latestRecords['news'][$key]['type']    = $news->type;
    
                    $latestRecords['news'][$key]['datetime'] = convertDateTimeFormat($news->publish_date,'fulldate');
    
                    $latestRecords['news'][$key]['image_url'] = $news->news_image_url ? $news->news_image_url : asset(config('constants.default.no_image'));

                    $latestRecords['news'][$key]['created_by'] = $news->user->name ?? null;

                }
                // End Latest News Details

                // Start Latest Seminar Details
                $latestSeminar = Seminar::select('id', 'title', 'total_ticket', 'ticket_price', 'start_date', 'start_time', 'end_time', 'venue')
                ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
                ->where(\DB::raw('CONCAT(start_date, " ", end_time)'), '>', now())
                ->where('status',1)
                ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')
                ->limit(1)
                ->first();
            
                if($latestSeminar){
                    $latestRecords['seminar']['id'] = $latestSeminar->id;
                    $latestRecords['seminar']['title'] = $latestSeminar->title;
                    $latestRecords['seminar']['ticket_price'] = $latestSeminar->ticket_price;
                    $latestRecords['seminar']['total_ticket'] = $latestSeminar->total_ticket;
                    $latestRecords['seminar']['venue'] = $latestSeminar->venue;
                    $latestRecords['seminar']['start_date'] = $latestSeminar->start_date;
                    $latestRecords['seminar']['start_time'] = $latestSeminar->start_time;
                    $latestRecords['seminar']['end_time'] = $latestSeminar->end_time;
    
                    $latestRecords['seminar']['datetime'] = convertDateTimeFormat($latestSeminar->start_date.' '.$latestSeminar->start_time,'fulldatetime') .'-'. Carbon::parse($latestSeminar->end_time)->format('h:i A');
    
                    $latestRecords['seminar']['imageUrl'] = $latestSeminar->image_url ? $latestSeminar->image_url : asset(config('constants.default.no_image'));
    
                    $latestRecords['seminar']['remain_ticket'] = (int)$latestSeminar->total_ticket - (int)$latestSeminar->bookings()->where('type','seminar')->count();   
                    
                    $seminarStartDateTime = Carbon::parse($latestSeminar->start_date . ' ' . $latestSeminar->start_time);

                    $latestRecords['seminar']['isBookingClosed'] = (((int)$latestRecords['seminar']['remain_ticket'] == 0) || (now() >= $seminarStartDateTime) ) ? true : false;
                }
              
                // End Latest Seminar Details


            }elseif($pageName == 'home'){
                // Start upcomming Seminar Details
                $upcomingSeminars = Seminar::select([
                    'id',
                    'title',
                    'total_ticket',
                    'ticket_price',
                    'start_date',
                    'start_time',
                    'end_time',
                    'venue',
                ])
                ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", start_time))) AS time_diff_seconds')
                ->whereRaw('CONCAT(start_date, " ", start_time) > NOW()')
                ->where('status',1)
                ->orderByRaw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds) ASC')
                ->limit(3)
                ->get();
              
                if($upcomingSeminars->count() > 0){

                    foreach($upcomingSeminars as $seminar){
                    
                        $seminar->datetime = convertDateTimeFormat($seminar->start_date.' '.$seminar->start_time,'fulldatetime') .'-'. Carbon::parse($seminar->end_time)->format('h:i A');
        
                        $seminar->imageUrl = $seminar->image_url ? $seminar->image_url : asset(config('constants.default.no_image'));

                        $seminar->remain_ticket = (int)$seminar->total_ticket - (int)$seminar->bookings()->where('type','seminar')->count();

                        $seminarStartDateTime = Carbon::parse($seminar->start_date . ' ' . $seminar->start_time);

                        $seminar->isBookingClosed = (((int)$seminar->remain_ticket == 0) || (now() >= $seminarStartDateTime) ) ? true : false;
                        
                    }

                    $latestRecords['upcomingSeminars'] = $upcomingSeminars;
                }
                // End upcomming Seminar Details

                // Start Latest Post Details

                $latestPosts = Post::select('id','title','slug','content','publish_date','type','created_by')->where('publish_date','<=',$currentDate)->whereIn('type',['news'])->orderBy('publish_date', 'desc')->limit(3)->get();

                if($latestPosts->count() > 0){
                    foreach($latestPosts as $post){
                  
                        $post->datetime = convertDateTimeFormat($post->publish_date,'fulldate');
        
                        if($post->type == 'news'){
                            $post->image_url = $post->news_image_url ? $post->news_image_url : asset(config('constants.default.no_image'));
                        }
    
                        $post->created_by  = $post->user->name ?? null;
                    }
    
                    $latestRecords['latestPosts'] = $latestPosts;    
                }
                // End Latest Post Details

            }

            // Return response
            $responseData = [
                'status' => true,
                'data'   => $latestRecords,
            ];
            return response()->json($responseData, 200);

        }catch (\Exception $e) { 
            //  dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }
  
    public function getLocations(){
        try {
            $locations = Location::whereStatus(1)->select('id', 'name')->get();

            // Return response
            $responseData = [
                'status' => true,
                'data'   => $locations,
            ];
            return response()->json($responseData, 200);
        } catch(\Exception $e){
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 500);
        }
    }
}
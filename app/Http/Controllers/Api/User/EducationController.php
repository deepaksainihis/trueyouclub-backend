<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index()
    {             
        try
        {
            $getAllRecords = Education::where('status',1)->orderBy('created_at', 'desc')->paginate(12);
            
            if ($getAllRecords->count() > 0)
            {
                foreach ($getAllRecords as $key=>$record)
                {                   
                    $record->formatted_date  = convertDateTimeFormat($record->created_at, 'fulldate');             
                    $record->image_url = $record->featured_image_url ? $record->featured_image_url : asset(config('constants.default.no_image')); 
                    $record->video_url = $record->educationVideo ?  $record->education_video_url : '';                                                
                    $record->created_by  = $record->user->name ?? null;                    
                    $record->makeHidden(['user', 'featuredImage','educationVideo']);
                    
                    $record->category->formatted_date  = convertDateTimeFormat($record->category->created_at, 'fulldate');            
                    $record->category->image_url = $record->featured_image_url ? $record->featured_image_url : asset(config('constants.default.no_image'));                   
                    $record->category->created_by  = $record->user->name ?? null;                    
                    $record->category->makeHidden(['user', 'featuredImage']);       
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
            ];
            return response()->json($responseData, 500);
        }
    }

    public function show(string $slug)
    { 
        $education = Education::where('slug',$slug)->where('status',1)->first();
        if($education){
            try{
                $responseData = [
                    'status'   => true,
                    'data'     => [                                 
                        'id' => $education->id,
                        'title' => $education->title ?? '',
                        'slug' => $education->slug ?? '',
                        'description' => $education->description ?? '',                        
                        'status' => $education->status ?? '',                            
                        'created_at' => $education->created_at->format('d-m-Y'),
                        'created_by' => $education->user->name ?? null,
                        'image_url' => $education->featured_image_url ? $education->featured_image_url : "",
                        "video_type" => $education->video_type ?? '',
                        'video_link' => $education->video_link ?? '',
                        'video_url' => $education->video_url = $education->educationVideo ?  $education->education_video_url : '',
                        'videoExtenstion' => $education->videoExtenstion = $education->educationVideo ?  $education->educationVideo->extension : '',                               
                        'category' => [
                            'id' => $education->category->id,
                            'name' => $education->category->name ?? '',
                            'slug' => $education->category->slug ?? '',
                            'description' => $education->category->description ?? '',
                            'status' => $record->category->status ?? '',
                            'created_at' => $education->category->created_at->format('d-m-Y H:i A'),
                            'created_by'  => $record->category->user->name ?? null,
                            'image_url' => $education->category->featured_image_url ? $education->category->featured_image_url : asset(config('constants.default.no_image')),  
                        ]     
                    ],
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
        }else{

            $responseData = [
                'status'  => false,
                'error'   => 'Record Does not exists',
            ];
            return response()->json($responseData, 500);
        }
        
    }

}

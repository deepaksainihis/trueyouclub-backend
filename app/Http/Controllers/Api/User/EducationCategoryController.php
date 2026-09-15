<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationCategoryController extends Controller
{
    public function index()
    {             
        try
        {
            $getAllRecords = Category::where('status',1)->orderBy('created_at', 'desc')->paginate(12);            
            
            if ($getAllRecords->count() > 0)
            {
                foreach ($getAllRecords as $key=>$record)
                {                   
                    $record->formatted_date  = convertDateTimeFormat($record->created_at, 'fulldate');             
                    $record->image_url = $record->featured_image_url ? $record->featured_image_url : asset(config('constants.default.no_image'));                   
                    $record->created_by  = $record->user->name ?? null;                    
                    $record->makeHidden(['user', 'featuredImage']);                               
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
        $category = Category::where('slug', $slug)->first();   
        $getAllRecords = Education::whereHas('category', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->where('status',1)->paginate(12);

        $category->category_image_url = $category->featured_image_url ? $category->featured_image_url : asset(config('constants.default.no_image'));  
        $category->makeHidden(['featuredImage']); 
      
        if($getAllRecords->count() > 0){
            try{
                foreach ($getAllRecords as $key=>$record)
                {                   
                    $record->formatted_date  = convertDateTimeFormat($record->created_at, 'fulldate');             
                    $record->image_url = $record->featured_image_url ? $record->featured_image_url : asset(config('constants.default.no_image')); 
                    $record->video_url = $record->educationVideo ?  $record->education_video_url : '';                                 
                    $record->videoExtenstion = $record->educationVideo ?  $record->educationVideo->extension : '';                                 
                    $record->created_by  = $record->user->name ?? null;                    
                    $record->makeHidden(['user', 'featuredImage','educationVideo']); 
                }      
                
                $responseData = [
                    'status'  => true,
                    'parent_data' => $category,                 
                    'data' => $getAllRecords               
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
                'parent_data' => $category, 
                'data'    => $getAllRecords,
                'message' => 'No Record Found',
            ];
            return response()->json($responseData, 200);
        }
        
    }

}

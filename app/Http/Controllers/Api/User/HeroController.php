<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Heroe;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    public function index($type=null)
    {
        try {

            if($type == 'home'){
                $getAllRecords = Heroe::select('id', 'name', 'slug', 'description', 'created_at', 'created_by')                
                ->orderBy('created_at', 'desc')
                ->limit(6)->get();
            }else{
                $getAllRecords = Heroe::select('id', 'name', 'slug', 'description', 'created_at', 'created_by')                
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            }
            

            if ($getAllRecords->count() > 0)
            {             
                foreach ($getAllRecords as $key=>$record)
                {                   
                    $record->created_at = convertDateTimeFormat($record->created_at, 'fulldate');             
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
        $hero = Heroe::where('slug',$slug)->where('status',1)->first();
        if($hero){
            try{
                $responseData = [
                    'status'       => true,
                    'data'         => [
                        'id'           => $hero->id,
                        'name'         => $hero->name,
                        'description'  => $hero->description,
                        'slug'         => $hero->slug,
                        'status'       => $hero->status,
                        'created_at'   => convertDateTimeFormat($hero->created_at, 'fulldate'),
                        'updated_at'   => convertDateTimeFormat($hero->updated_at, 'fulldate'),
                        'image_url'    => $hero->featured_image_url ? $hero->featured_image_url : asset(config('constants.default.no_image')),
                        'created_by'   => $hero->user->name ?? null,                       
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

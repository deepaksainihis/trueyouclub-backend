<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


class PostController extends Controller
{
    public function index($postType)
    {

        try {

            $currentDate = now()->toDateString();

            $getAllRecords = Post::where('type', $postType)
                ->select('id', 'title', 'slug', 'content', 'publish_date', 'type', 'created_by')
                ->whereDate('publish_date', '<=', $currentDate)
                ->orderBy('publish_date', 'desc')
                ->paginate(12);

            if ($getAllRecords->count() > 0) {

                foreach ($getAllRecords as $key=>$record) {
                   
                    $record->publish_date = convertDateTimeFormat($record->publish_date, 'fulldate');

                    if ($record->type == 'news') {

                        $record->image_url = $record->news_image_url ? $record->news_image_url : asset(config('constants.default.no_image'));

                    } else if ($record->type == 'health') {

                        $record->image_url = $record->health_image_url ? $record->health_image_url : asset(config('constants.default.no_image'));
                    }

                    $record->created_by  = $record->user->name ?? null;
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

    public function show($slug)
    {

        try {
            $record = Post::select('id', 'title', 'slug', 'content', 'publish_date', 'type', 'created_by')->where('slug', $slug)->first();

            if ($record) {

                $record->publish_date = convertDateTimeFormat($record->publish_date, 'fulldate');

                if ($record->type == 'news') {

                    $record->image_url = $record->news_image_url ? $record->news_image_url : asset(config('constants.default.no_image'));
                    
                } else if ($record->type == 'health') {

                    $record->image_url = $record->health_image_url ? $record->health_image_url : asset(config('constants.default.no_image'));
                }

                $record->created_by  = $record->user->name ?? null;

                $responseData = [
                    'status'  => true,
                    'data'    => $record,
                ];
                return response()->json($responseData, 200);
            } else {
                $responseData = [
                    'status'  => false,
                    'message' => 'No Record Found',
                ];
                return response()->json($responseData, 404);
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

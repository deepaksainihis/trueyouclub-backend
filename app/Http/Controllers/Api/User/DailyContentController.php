<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyContent;
use Carbon\Carbon;

class DailyContentController extends Controller
{
    public function today()
    {
        $contents = DailyContent::whereDate('scheduled_date', Carbon::today())->get();

        // Append full URL for media_path if it exists
        $contents->transform(function($content) {
            if ($content->media_path) {
                $content->media_url = asset('storage/' . $content->media_path);
            }
            return $content;
        });

        return response()->json([
            'status' => 200,
            'data' => $contents
        ]);
    }
}

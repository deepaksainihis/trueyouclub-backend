<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feed;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'status' => true,
            'data' => $feeds
        ]);
    }
}

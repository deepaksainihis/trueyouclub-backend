<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserBadgeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Load badges with pivot data (user_badges) to get the date it was awarded
        $badges = $user->badges()->orderBy('user_badges.created_at', 'desc')->get();
        
        return response()->json([
            'status' => 200,
            'data' => $badges
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::whereHas('roles', function($q) {
                $q->where('roles.id', 2);
            })
            ->orderBy('points', 'desc')
            ->select('id', 'name', 'points')
            ->take(50)
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }
}

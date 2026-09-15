<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Support\Facades\Validator;

class GamificationController extends Controller
{
    public function leaderboard(Request $request)
    {
        $users = User::whereHas('roles', function ($query) {
                $query->where('roles.id', 2);
            })
            ->select('id', 'name', 'points')
            ->orderBy('points', 'desc')
            ->take(10)
            ->get();
            
        return response()->json(['status' => 200, 'data' => $users]);
    }

    public function rewards(Request $request)
    {
        $rewards = Reward::all();
        return response()->json(['status' => 200, 'data' => $rewards]);
    }

    public function redeem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reward_id' => 'required|exists:rewards,id',
            'shipping_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
        }

        $user = $request->user();
        $reward = Reward::find($request->reward_id);

        if ($user->points < $reward->points_cost) {
            return response()->json(['status' => 400, 'message' => 'Not enough points to redeem this reward.']);
        }

        if ($reward->stock <= 0) {
            return response()->json(['status' => 400, 'message' => 'This reward is out of stock.']);
        }

        // Deduct points and stock
        $user->points -= $reward->points_cost;
        $user->save();

        $reward->stock -= 1;
        $reward->save();

        $redemption = RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
        ]);

        return response()->json(['status' => 200, 'message' => 'Reward redeemed successfully!', 'data' => $redemption]);
    }
}

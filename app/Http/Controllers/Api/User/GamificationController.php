<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\HabitLog;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GamificationController extends Controller
{
    private function getPointsEarnedToday($userId)
    {
        $today = Carbon::today()->format('Y-m-d');
        $todayLogs = HabitLog::where('user_id', $userId)
            ->where('log_date', $today)
            ->where('status', 'completed')
            ->get();

        $points = 0;

        foreach ($todayLogs as $log) {
            $points += 10;
            
            $allLogs = HabitLog::where('user_id', $userId)
                ->where('habit_id', $log->habit_id)
                ->where('status', 'completed')
                ->orderBy('log_date', 'desc')
                ->get();
                
            $currentStreak = 0;
            $lastCheckedDate = Carbon::today();
            $isStreakActive = true;
            
            foreach ($allLogs as $l) {
                $date = Carbon::parse($l->log_date);
                if ($lastCheckedDate->diffInDays($date) > 1) {
                    if ($isStreakActive) {
                        $isStreakActive = false;
                    }
                }
                if ($isStreakActive) {
                    $currentStreak++;
                }
                $lastCheckedDate = $date;
            }
            
            if ($currentStreak > 0 && $currentStreak % 7 === 0) {
                $points += 50;
            }
        }
        
        return $points;
    }
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

        $pointsEarnedToday = $this->getPointsEarnedToday($user->id);
        $redeemablePoints = max(0, $user->points - $pointsEarnedToday);

        if ($user->points < $reward->points_cost) {
            return response()->json(['status' => 400, 'message' => 'Not enough points to redeem this reward.']);
        }

        if ($redeemablePoints < $reward->points_cost) {
            return response()->json(['status' => 400, 'message' => 'You cannot redeem rewards using points earned today.']);
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

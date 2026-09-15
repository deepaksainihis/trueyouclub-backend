<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HabitController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $habits = Habit::where('user_id', $user->id)->get();
        $today = Carbon::today()->toDateString();
        
        $habitsData = $habits->map(function ($habit) use ($today) {
            $isCompletedToday = HabitLog::where('habit_id', $habit->id)
                ->where('completed_date', $today)
                ->exists();

            $streak = 0;
            $currentStreak = 0;
            $dateToMatch = $isCompletedToday ? Carbon::today() : Carbon::yesterday();
            
            $logs = HabitLog::where('habit_id', $habit->id)
                ->orderBy('completed_date', 'desc')
                ->get();
                
            foreach ($logs as $log) {
                if ($log->completed_date == $dateToMatch->toDateString()) {
                    $currentStreak++;
                    $dateToMatch->subDay();
                } else {
                    break;
                }
            }
            $streak = $currentStreak;

            return [
                'id' => $habit->id,
                'name' => $habit->name,
                'is_completed_today' => $isCompletedToday,
                'streak' => $streak
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $habitsData
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $habit = Habit::create([
            'user_id' => $user->id,
            'name' => $request->name
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Habit created successfully',
            'data' => $habit
        ]);
    }

    public function check($id)
    {
        $user = Auth::user();
        $habit = Habit::where('user_id', $user->id)->findOrFail($id);
        $today = Carbon::today()->toDateString();

        $log = HabitLog::where('habit_id', $habit->id)
            ->where('completed_date', $today)
            ->first();

        if ($log) {
            $log->delete();
            $user->points = max(0, $user->points - 10);
            $user->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Habit unchecked for today.',
                'points' => $user->points
            ]);
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'completed_date' => $today
            ]);
            $user->points += 10;
            $user->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Habit completed for today!',
                'points' => $user->points
            ]);
        }
    }
}

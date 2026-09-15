<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class HabitController extends Controller
{
    public function index(Request $request)
    {
        $habits = Habit::where('user_id', $request->user()->id)->where('status', true)->get();
        return response()->json(['status' => 200, 'data' => $habits]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'frequency' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()]);
        }

        $habit = Habit::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'frequency' => $request->frequency ?? 'daily',
        ]);

        return response()->json(['status' => 200, 'message' => 'Habit created successfully.', 'data' => $habit]);
    }

    public function destroy(Request $request, $id)
    {
        $habit = Habit::where('id', $id)->where('user_id', $request->user()->id)->first();
        if ($habit) {
            $habit->delete();
            return response()->json(['status' => 200, 'message' => 'Habit deleted successfully.']);
        }
        return response()->json(['status' => 404, 'message' => 'Habit not found.']);
    }

    public function today(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $habits = Habit::where('user_id', $request->user()->id)->where('status', true)->get();
        
        $logs = HabitLog::where('user_id', $request->user()->id)
            ->whereIn('habit_id', $habits->pluck('id'))
            ->orderBy('log_date', 'desc')
            ->get()
            ->groupBy('habit_id');

        $habitsWithStatus = $habits->map(function ($habit) use ($logs, $today) {
            $habitLogs = isset($logs[$habit->id]) ? $logs[$habit->id] : collect();
            
            $habit->completed_today = $habitLogs->contains(function ($log) use ($today) {
                return $log->log_date->format('Y-m-d') === $today && $log->status === 'completed';
            });
            
            $currentStreak = 0;
            $longestStreak = 0;
            $tempStreak = 0;
            $lastCheckedDate = Carbon::today();
            $isStreakActive = true;
            
            foreach ($habitLogs->where('status', 'completed') as $log) {
                $date = Carbon::parse($log->log_date);
                
                if ($lastCheckedDate->diffInDays($date) > 1) {
                    if ($isStreakActive) {
                        $isStreakActive = false;
                    }
                    $tempStreak = 0;
                }
                
                if ($isStreakActive) {
                    $currentStreak++;
                }
                $tempStreak++;
                if ($tempStreak > $longestStreak) {
                    $longestStreak = $tempStreak;
                }
                $lastCheckedDate = $date;
            }
            
            $completedToday = $habitLogs->contains(function ($log) use ($today) { return $log->log_date->format('Y-m-d') === $today && $log->status === 'completed'; });
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $completedYesterday = $habitLogs->contains(function ($log) use ($yesterday) { return $log->log_date->format('Y-m-d') === $yesterday && $log->status === 'completed'; });
            if (!$completedToday && !$completedYesterday) {
                $currentStreak = 0;
            }
            
            $habit->current_streak = $currentStreak;
            $habit->longest_streak = max($longestStreak, $currentStreak);
            
            return $habit;
        });

        return response()->json(['status' => 200, 'data' => $habitsWithStatus]);
    }

    public function checkIn(Request $request, $id)
    {
        $today = Carbon::today()->format('Y-m-d');
        $habit = Habit::where('id', $id)->where('user_id', $request->user()->id)->first();
        
        if (!$habit) {
            return response()->json(['status' => 404, 'message' => 'Habit not found.']);
        }

        $log = HabitLog::where('habit_id', $id)
            ->where('user_id', $request->user()->id)
            ->where('log_date', $today)
            ->first();

        $completed = false;
        $user = $request->user();

        if ($log) {
            if ($log->status === 'completed') {
                $log->delete();
                $completed = false;
                $user->points = max(0, $user->points - 10);
                $user->save();
            } else {
                $log->status = 'completed';
                $log->save();
                $completed = true;
                $user->points += 10;
            }
        } else {
            HabitLog::create([
                'habit_id' => $id,
                'user_id' => $user->id,
                'log_date' => $today,
                'status' => 'completed'
            ]);
            $completed = true;
            $user->points += 10;
        }

        if ($completed) {
            $streak = $this->calculateHabitStreak($id, $user->id);
            if ($streak > 0 && $streak % 7 === 0) {
                $user->points += 50;
            }
            $user->save();
        }
        
        $user->checkAndAwardBadges();

        return response()->json(['status' => 200, 'message' => 'Habit status updated.', 'completed' => $completed, 'points' => $user->points]);
    }

    private function calculateHabitStreak($habitId, $userId)
    {
        $logs = HabitLog::where('user_id', $userId)
            ->where('habit_id', $habitId)
            ->where('status', 'completed')
            ->orderBy('log_date', 'desc')
            ->get();
            
        $currentStreak = 0;
        $lastCheckedDate = Carbon::today();
        $todayStr = Carbon::today()->format('Y-m-d');
        $isStreakActive = true;
        
        foreach ($logs as $log) {
            $date = Carbon::parse($log->log_date);
            
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
        
        return $currentStreak;
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;
        
        $logsByDate = HabitLog::where('user_id', $userId)
            ->where('status', 'completed')
            ->selectRaw('log_date, count(*) as completed_count')
            ->groupBy('log_date')
            ->orderBy('log_date', 'desc')
            ->get();

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        $lastCheckedDate = Carbon::today();
        
        $todayStr = Carbon::today()->format('Y-m-d');
        $yesterdayStr = Carbon::yesterday()->format('Y-m-d');
        
        $isStreakActive = true;

        foreach ($logsByDate as $log) {
            $date = Carbon::parse($log->log_date);
            
            if ($lastCheckedDate->diffInDays($date) > 1) {
                 if ($isStreakActive) {
                     $isStreakActive = false;
                 }
                 $tempStreak = 0;
            }

            if ($log->completed_count >= 1) {
                if ($isStreakActive) {
                    $currentStreak++;
                }
                $tempStreak++;
                if ($tempStreak > $longestStreak) {
                    $longestStreak = $tempStreak;
                }
            }
            $lastCheckedDate = $date;
        }
        
        $completedToday = $logsByDate->where('log_date', $todayStr)->first();
        $completedYesterday = $logsByDate->where('log_date', $yesterdayStr)->first();
        
        if (!$completedToday && !$completedYesterday) {
            $currentStreak = 0;
        }

        return response()->json([
            'status' => 200, 
            'data' => [
                'current_streak' => $currentStreak, 
                'longest_streak' => max($longestStreak, $currentStreak)
            ]
        ]);
    }
}

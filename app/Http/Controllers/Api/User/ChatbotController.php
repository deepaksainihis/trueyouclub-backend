<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function askCoach(Request $request)
    {
        $request->validate([
            'messages' => 'required|array'
        ]);

        $user = auth('sanctum')->user();

        if (!$user && $request->bearerToken()) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable;
            }
        }

        $userContext = "You are speaking with a guest user who is not logged in. Encourage them to sign up to track their habits and goals.";
        if ($user) {
            $name = $user->first_name ?? $user->name ?? 'User';
            $points = $user->points ?? 0;

            // Fetch habits and calculate streaks
            $habitsText = "";
            if (class_exists(\App\Models\Habit::class) && class_exists(\App\Models\HabitLog::class)) {
                $habits = \App\Models\Habit::where('user_id', $user->id)->get();
                $today = \Carbon\Carbon::today()->toDateString();

                if ($habits->count() > 0) {
                    foreach ($habits as $habit) {
                        $isCompletedToday = \App\Models\HabitLog::where('habit_id', $habit->id)
                            ->where('completed_date', $today)
                            ->exists();

                        $currentStreak = 0;
                        $dateToMatch = $isCompletedToday ? \Carbon\Carbon::today() : \Carbon\Carbon::yesterday();

                        $logs = \App\Models\HabitLog::where('habit_id', $habit->id)
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

                        $completedStr = $isCompletedToday ? 'Yes' : 'No';
                        $habitsText .= "- {$habit->name} (Completed Today: {$completedStr}, Streak: {$currentStreak} days)\n";
                    }
                } else {
                    $habitsText = "User has not set up any habits yet.\n";
                }
            }

            $userContext = "You are speaking with a logged-in user named {$name}. Use their name occasionally and tailor advice to their personal growth.\n\n";
            $userContext .= "CURRENT USER DATA (Use this to give personalized advice):\n";
            $userContext .= "- Total Reward Points: {$points}\n";
            $userContext .= "- Active Habits:\n{$habitsText}";
        }

        $groqApiKey = env('GROQ_API_KEY');
        $groqApiUrl = 'https://api.groq.com/openai/v1/chat/completions';

        $systemPrompt = [
            'role' => 'system',
            'content' => "You are the True You Club AI Coach, an expert in personal growth. Your purpose is to help users with personal growth using frameworks such as Socratic questioning, goal setting, and CBT-style journaling prompts.

$userContext

CRITICAL INSTRUCTIONS:
1. ONLY answer questions related to the True You Club platform, personal growth, habits, mindset, and coaching.
2. If a user asks a general knowledge question, you MUST politely decline and steer the conversation back to their personal growth or the platform.
3. Keep your answers concise, empathetic, and action-oriented.
4. When appropriate, use Socratic questioning to help the user find their own answers.
5. You act as a coaching/assistance layer. You do NOT independently modify user records (like creating habits or goals) unless explicitly defined.

True You Club Platform Context (Simulated RAG Knowledge Base):
The backend provides the following context about the platform that users have access to:
- Habits (tracking daily habits)
- Checklist status
- Progress & Streaks (consistency tracking)
- Points, Badges, and Rewards (earned for completing activities)
- Goals (users can set and track goals)
- Relevant motivational content
- A Feed to connect and reflect with updates
- Calendar events and reminders for the personal growth journey

Always format your response clearly, using new lines for readability, but DO NOT use markdown like asterisks (**) for bolding, as the current UI does not render markdown perfectly. Use plain text formatting."
        ];

        $messages = $request->input('messages');
        array_unshift($messages, $systemPrompt);

        $modelsToTry = ['openai/gpt-oss-20b', 'qwen/qwen3.8-27b'];
        $replyContent = "I'm having a little trouble connecting right now. Please try again in a moment.";

        $allErrors = [];

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $groqApiKey,
                    'Content-Type' => 'application/json'
                ])->post($groqApiUrl, [
                            'model' => $model,
                            'messages' => $messages,
                            'temperature' => 0.7,
                            'max_tokens' => 1024,
                        ]);

                if ($response->successful()) {
                    $content = $response->json()['choices'][0]['message']['content'];
                    // Strip Markdown asterisks since UI doesn't render them
                    $content = str_replace(['**', '*'], '', $content);
                    return response()->json([
                        'status' => true,
                        'message' => $content
                    ]);
                } else {
                    $allErrors[$model] = $response->json();
                }
            } catch (\Exception $e) {
                Log::error("Groq API Error with model $model: " . $e->getMessage());
                $allErrors[$model] = $e->getMessage();
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'All models failed',
            'debug' => $allErrors
        ], 200);
    }
}

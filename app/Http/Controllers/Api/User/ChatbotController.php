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

        $groqApiKey = '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';
        $groqApiUrl = 'https://api.groq.com/openai/v1/chat/completions';

        $systemPrompt = [
            'role' => 'system',
            'content' => "You are the True You Club AI Coach, an expert in personal growth. Your purpose is to help users with personal growth using frameworks such as Socratic questioning, goal setting, and CBT-style journaling prompts.

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

        $modelsToTry = ['qwen/qwen3.8-27b', 'openai/gpt-oss-20b', 'groq/compound'];
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
                    return response()->json([
                        'status' => true,
                        'message' => $response->json()['choices'][0]['message']['content']
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

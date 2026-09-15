<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyContent;
use App\Models\User;
use Carbon\Carbon;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushDailyContent extends Command
{
    protected $signature = 'push:daily-content';
    protected $description = 'Send daily content push notifications to all users';

    public function handle()
    {
        $todayContent = DailyContent::whereDate('scheduled_date', Carbon::today())->first();

        if (!$todayContent) {
            $this->info('No content scheduled for today.');
            return;
        }

        $users = User::whereNotNull('fcm_token')->get();
        if ($users->isEmpty()) {
            $this->info('No users with FCM tokens found.');
            return;
        }

        $credentialsPath = storage_path('firebase_credentials.json');
        if (!file_exists($credentialsPath)) {
            $this->error("Firebase credentials file not found at: {$credentialsPath}");
            $this->info("Please place your Firebase Admin SDK JSON file there.");
            return;
        }

        // Get Google OAuth Token
        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();
        $accessToken = $token['access_token'];

        // Get Project ID from JSON
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        $projectId = $credentials['project_id'];
        
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $title = "🌟 " . $todayContent->title;
        $body = "Check out your new Daily Motivation!";
        
        if ($todayContent->type === 'micro_learning') {
            $body = \Illuminate\Support\Str::limit($todayContent->description, 100);
        }

        $successCount = 0;
        foreach ($users as $user) {
            $message = [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'type' => 'daily_content',
                        'content_id' => (string) $todayContent->id
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $message);

            if ($response->successful()) {
                $successCount++;
            } else {
                Log::error("FCM Send Error for user {$user->id}: " . $response->body());
            }
        }

        $this->info("Sent {$successCount} push notifications.");
    }
}

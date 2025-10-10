<?php

namespace App\Services;

use Google\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public static function send($deviceToken, $title, $body)
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS', base_path('public/firebase/firebase.json'));

        if (!file_exists($credentialsPath)) {
            Log::error('Firebase credentials file not found', ['path' => $credentialsPath]);
            return null;
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        $projectId = json_decode(file_get_contents($credentialsPath), true)['project_id'];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $http = new HttpClient();

        try {
            $response = $http->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'android' => [
                            'priority' => 'HIGH',
                            'notification' => [
                                'channel_id' => 'high_importance_channel',
                                'sound' => 'default',
                            ],
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $firebaseResponse = json_decode((string) $response->getBody(), true);

            Log::info('Firebase Response', [
                'response' => $firebaseResponse,
                'token' => $deviceToken,
                'title' => $title,
                'body' => $body,
            ]);

            return $firebaseResponse;
        } catch (\Exception $e) {
            Log::error('Firebase Send Exception', [
                'error' => $e->getMessage(),
                'token' => $deviceToken,
                'title' => $title,
                'body' => $body,
            ]);
            return null;
        }
    }
}

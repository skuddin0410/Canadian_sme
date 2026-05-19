<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CometChatService
{
    public function createUser(User $user): ?array
    {
        try {
            $appID = env('COMETCHAT_APP_ID');
            $apiKey = env('COMETCHAT_API_KEY');
            $region = env('COMETCHAT_REGION');

            if (empty($appID) || empty($apiKey) || empty($region)) {
                Log::warning('CometChat credentials are missing.', [
                    'user_id' => $user->id,
                ]);

                return null;
            }

            $avatarUrl = $user->photo ? $user->photo->mobile_path : asset('images/noImage.png');

            $data = [
                'uid' => "SME_CometChat_{$user->id}",
                'name' => $user->name ?? '',
                'avatar' => $avatarUrl,
                'role' => 'default',
                'statusMessage' => 'default',
                'metadata' => [
                    '@private' => [
                        'email' => $user->email,
                        'contactNumber' => $user->mobile,
                    ],
                ],
                'tags' => [],
                'withAuthToken' => true,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $apiKey,
            ])->post("https://{$appID}.api-{$region}.cometchat.io/v3/users", $data);

            if (! $response->successful()) {
                Log::error('CometChat user creation failed.', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $responseData = $response->json();

            return [
                'uid' => $responseData['data']['uid'] ?? null,
                'status' => $responseData['data']['status'] ?? null,
                'authToken' => $responseData['data']['authToken'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error("Error creating CometChat user for user ID {$user->id}: {$e->getMessage()}");

            return null;
        }
    }
}

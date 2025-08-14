<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Property;
use App\Http\Controllers\PropertyMatchingController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePropertyMatchesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;

    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }

    public function handle(PropertyMatchingController $controller)
    {
        $properties = Property::where('status', 'available')->get();
        
        foreach ($this->userIds as $userId) {
            try {
                $user = User::with('investorProfile')->find($userId);
                
                if (!$user || !$user->investorProfile) {
                    continue;
                }

                // Generate matches for this investor
                $matches = $controller->generateMatchesForInvestor(
                    $user->investorProfile, 
                    $properties
                );

                // Store matches would be called here
                // $controller->storeMatches($userId, $matches);

                Log::info("Generated matches for user {$userId}", [
                    'user_id' => $userId,
                    'matches_count' => count($matches)
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to generate matches for user {$userId}", [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
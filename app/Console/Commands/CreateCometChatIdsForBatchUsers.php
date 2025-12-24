<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
 
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
 
class CreateCometChatIdsForBatchUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-cometchat-ids-batch {batch_size=100}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign CometChat IDs to users in batches';
 
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = (int) $this->argument('batch_size');
        // dd($batchSize);
        $processedCount = 0;
 
        // Get users without a CometChat ID, in batches
        $users = User::whereNull('cometchat_id')->chunk($batchSize, function ($users) use (&$processedCount) {
            // dd($users);
            Log::info('Chunk of users fetched for CometChat ID creation: ' . count($users));
            foreach ($users as $user) {
                // Call the method to create CometChat user and get the response
                $cometChatResponse = $this->createCometChatUser($user->id, $user->name, $user->email, $user->mobile);
               
                if ($cometChatResponse) {
                    // Update the user with CometChat details
                    $user->cometchat_id = $cometChatResponse['uid'];
                    // $user->cometchat_status = $cometChatResponse['status'];
                    // $user->cometchat_auth_token = $cometChatResponse['authToken'];
                    $user->save();
 
                    // Output a success message
                    $this->info("CometChat ID created for user: {$user->id} ({$user->name})");
                } else {
                    // Output a failure message if the user creation fails
                    $this->error("Failed to create CometChat ID for user: {$user->id} ({$user->name})");
                }
 
                $processedCount++;
            }
        });
 
        $this->info("Batch processing completed. Total users processed: {$processedCount}");
    }
 
    /**
     * Method to call the createCometChatUser logic
     *
     * @param int $userId
     * @param string $name
     * @param string $email
     * @return array|null
     */
    private function createCometChatUser($userId, $name, $email, $mobile)
    {
        try{
            $appID = env('COMETCHAT_APP_ID');
            $apiKey = env('COMETCHAT_API_KEY');
            $region = env('COMETCHAT_REGION');
 
            // Get the user's photo URL using the `photo` relationship
            $user = User::find($userId);
            $avatarUrl = $user->photo ? $user->photo->mobile_path : asset('images/default.png');
 
            $data = [
                'uid' => "SME_CometChat_{$userId}",
                'name' => $name ?? '',
                'avatar' => $avatarUrl,
                // 'link' => "https://commons.wikimedia.org/wiki/File:No_Image_Available.jpg",
                'role' => 'default',
                'statusMessage' => 'default',
                'metadata' => [
                    '@private' => [
                        'email' => $email,
                        'contactNumber' => $mobile,
                    ]
                ],
                'tags' => [],
                'withAuthToken' => true
            ];
 
            // Make API call to create the CometChat user
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $apiKey,
            ])->post(
                "https://{$appID}.api-{$region}.cometchat.io/v3/users",
                $data
            );
 
            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'uid' => $responseData['data']['uid'],
                    'status' => $responseData['data']['status'],
                    'authToken' => $responseData['data']['authToken']
                ];
            }
 
            return null;
        } catch (\Exception $e) {
            Log::error("Error creating CometChat user for user ID {$userId}: " . $e->getMessage());
            return null;
        }  
    }
}
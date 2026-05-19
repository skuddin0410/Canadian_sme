<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use App\Jobs\CreateCometChatUserJob;
use App\Models\User;
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
        $dispatchedCount = 0;

        User::whereNull('cometchat_id')->chunk($batchSize, function ($users) use (&$dispatchedCount) {
            Log::info('Chunk of users fetched for CometChat job dispatch: ' . count($users));
            foreach ($users as $user) {
                CreateCometChatUserJob::dispatch($user->id);
                $dispatchedCount++;
            }
        });

        $this->info("Queued CometChat creation jobs: {$dispatchedCount}");
    }
}

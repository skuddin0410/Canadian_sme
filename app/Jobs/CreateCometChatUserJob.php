<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CometChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCometChatUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public int $userId)
    {
    }

    public function handle(CometChatService $cometChatService): void
    {
        $user = User::find($this->userId);

        if (! $user || ! empty($user->cometchat_id)) {
            return;
        }

        $cometChatResponse = $cometChatService->createUser($user);

        if (! empty($cometChatResponse['uid'])) {
            $user->cometchat_id = $cometChatResponse['uid'];
            $user->save();
        }
    }
}

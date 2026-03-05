<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class SendScheduledBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userIds;
    public $templateName;

    /**
     * Create a new job instance.
     */
    public function __construct(array $userIds, string $templateName)
    {
        $this->userIds = $userIds;
        $this->templateName = $templateName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationTemplate = EmailTemplate::where('template_name', $this->templateName)
            ->where('type', 'notifications')
            ->first();

        if (!$notificationTemplate) {
            Log::error("Scheduled notification failed: Template '{$this->templateName}' not found.");
            return;
        }

        $usersQuery = User::query();
        if (!in_array('all', $this->userIds)) {
            $usersQuery->whereIn('id', $this->userIds);
        }

        $usersQuery->chunk(200, function ($users) use ($notificationTemplate) {
            foreach ($users as $user) {
                $title = 'Hi, ' . ($user->name ?? $user->email) . ',';
                $message = str_replace(
                    ['{{name}}', '{{ name }}', '{{qr_code}}', '{{ qr_code }}', '{{profile_update_link}}', '{{ profile_update_link }}'],
                    [
                        $user->name ?? $user->email,
                        $user->name ?? $user->email,
                        $user->qr_code ?? '',
                        $user->qr_code ?? '',
                        route('update-user', Crypt::encryptString($user->id)),
                        route('update-user', Crypt::encryptString($user->id))
                    ],
                    $notificationTemplate->message ?? ''
                );

                // 1. Create internal database notification record via GeneralNotification model
                \App\Models\GeneralNotification::create([
                    'user_id' => $user->id,
                    'title'   => $notificationTemplate->title ?? $title,
                    'body'    => $message,
                    'is_read' => 0,
                    'delivered_at' => now(),
                ]);

                // 2. Send OneSignal push notification
                if (!empty($user->onesignal_userid)) {
                    $this->sendOneSignalPush($user->onesignal_userid, $notificationTemplate->title ?? $title, $message);
                }
            }
        });
    }

    protected function sendOneSignalPush($playerId, $title, $message)
    {
        $appId = trim(env('ONESIGNAL_APP_ID'));
        $apiKey = trim(env('ONESIGNAL_REST_API_KEY'));

        if (empty($appId) || empty($apiKey)) {
            Log::error('OneSignal credentials missing in .env');
            return;
        }

        $payload = [
            'app_id'             => $appId,
            'include_player_ids' => [$playerId],
            'headings'           => ['en' => $title],
            'contents'           => ['en' => $message],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', $payload);

            if (!$response->successful()) {
                Log::error('OneSignal push failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'player_id' => $playerId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OneSignal push exception: ' . $e->getMessage());
        }
    }
}

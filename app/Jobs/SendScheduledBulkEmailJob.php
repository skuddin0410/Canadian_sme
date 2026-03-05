<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\EmailTemplate;
use App\Mail\UserWelcome;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class SendScheduledBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userIds;
    public $templateName;

    public function __construct(array $userIds, string $templateName)
    {
        $this->userIds = $userIds;
        $this->templateName = $templateName;
    }

    public function handle()
    {
        $emailTemplate = EmailTemplate::where('template_name', $this->templateName)->first();

        if (!$emailTemplate || $emailTemplate->type !== 'email') {
            Log::error("Scheduled email: Template '{$this->templateName}' not found or not email type.");
            return;
        }

        $subject = str_replace('{{site_name}}', config('app.name'), $emailTemplate->subject ?? '');

        $usersQuery = in_array('all', $this->userIds)
            ? User::query()
            : User::whereIn('id', $this->userIds);

        $usersQuery->chunk(100, function ($users) use ($emailTemplate, $subject) {
            foreach ($users as $user) {
                try {
                    $qr_code_url = asset($user->qr_code);
                    $message = $emailTemplate->message ?? '';
                    $message = str_replace('{{name}}', $user->full_name, $message);
                    $message = str_replace('{{site_name}}', config('app.name'), $message);

                    if (strpos($message, '{{qr_code}}') !== false) {
                        $message = str_replace('{{qr_code}}', '<br><img src="' . $qr_code_url . '" alt="QR Code" />', $message);
                    }

                    if (strpos($message, '{{profile_update_link}}') !== false) {
                        $updateUrl = route('update-user', Crypt::encryptString($user->id));
                        $message = str_replace('{{profile_update_link}}', '<br><a href="' . $updateUrl . '">Update Profile</a>', $message);
                    }

                    Mail::to($user->email)->send(new UserWelcome($user, $subject, $message));
                } catch (\Exception $e) {
                    Log::error("Scheduled email failed for user {$user->id}: " . $e->getMessage());
                }
            }
        });
    }
}

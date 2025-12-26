<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use App\Jobs\UpdateUserQrCodeJob;

class UsersImport implements ToModel, WithStartRow, WithEvents
{
    protected $users = [];
    protected $emails = [];       // Track all processed emails
    protected $duplicates = [];   // Track duplicate emails
    protected $added = [];        // Track successfully added emails

    public function __construct()
    {
        $this->emails = User::pluck('email')->map(fn($e) => trim($e))->toArray();
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
            return null;
        }

        $email = trim($row[2]);

        // Case-insensitive duplicate check
        if (in_array(strtolower($email), array_map('strtolower', $this->emails))) {
            $this->duplicates[] = $email;
            return null; // Skip duplicate emails
        }

        // Check if email exists in DB (case-insensitive)
        $existingUser = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
        if ($existingUser) {
            $this->duplicates[] = $email;
            return null;
        }

        // Mark as processed
        $this->emails[] = $email;
        $this->added[] = $email;

        // Add to batch for bulk insert
        $this->users[] = [
            'name' => trim($row[0]),
            'lastname' => trim($row[1]),
            'email' => $email,
            'status' => $row[3] ?? '',
            'gdpr_consent' => (isset($row[4]) && strtolower(trim($row[4])) === 'confirmed') ? 1 : 0,
            'bio' => $row[5] ?? '',
            'company' => $row[6] ?? '',
            'designation' => $row[7] ?? '',
            'mobile' => $row[8] ?? '',
            'dob' => $row[9] ?? '',
            'facebook_url' => $row[10] ?? '',
            'twitter_url' => $row[11] ?? '',
            'linkedin_url' => $row[12] ?? '',
            'instagram_url' => $row[13] ?? '',
            'slug' => createUniqueSlug('users', $row[0] . '_' . $row[1]),
            'primary_group' => 'Attendee',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert batch every 500 rows
        if (count($this->users) >= 500) {
            User::insert($this->users);
            $this->users = [];
        }

        return null;
    }



    public function __destruct()
    {
        // Insert remaining users
        if (count($this->users) > 0) {
            User::insert($this->users);
        }

        // Log results
        if (!empty($this->added)) {
           // Log::info('Imported Users:', $this->added);
        }

        if (!empty($this->duplicates)) {
           // Log::info('Duplicate/Skipped Users:', $this->duplicates);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                //UpdateUserQrCodeJob::dispatch();
                dispatch(new UpdateUserQrCodeJob());
            },
        ];
    }
}

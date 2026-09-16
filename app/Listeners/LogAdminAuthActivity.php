<?php

namespace App\Listeners;

use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAdminAuthActivity
{
    public function handleLogin(Login $event): void
    {
        $user = $event->user instanceof User ? $event->user : null;

        if (!$user || !AdminActivityLogger::isAdminPanelUser($user)) {
            return;
        }

        // Admin portal login only
        if (!$this->isAdminLoginRequest()) {
            return;
        }

        AdminActivityLogger::logAuth('login', $user, [
            'status_code' => 200,
            'meta' => ['guard' => $event->guard ?? 'web'],
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        $user = $event->user instanceof User ? $event->user : null;

        if (!$user || !AdminActivityLogger::isAdminPanelUser($user)) {
            return;
        }

        AdminActivityLogger::logAuth('logout', $user, [
            'status_code' => 200,
            'meta' => ['guard' => $event->guard ?? 'web'],
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        if (!$this->isAdminLoginRequest()) {
            return;
        }

        $email = $event->credentials['email'] ?? ($event->credentials['username'] ?? 'unknown');
        $user = $event->user instanceof User ? $event->user : null;

        if (!$user && is_string($email) && $email !== 'unknown') {
            $user = User::where('email', $email)->orWhere('username', $email)->first();
        }

        AdminActivityLogger::logAuth('login_failed', $user, [
            'actor_email' => is_string($email) ? $email : 'unknown',
            'description' => 'Failed admin login attempt for ' . (is_string($email) ? $email : 'unknown'),
            'status_code' => 401,
            'meta' => [
                'email' => $email,
                'guard' => $event->guard ?? 'web',
            ],
        ]);
    }

    private function isAdminLoginRequest(): bool
    {
        $request = request();

        return $request->is('admin/login')
            || $request->is('admin')
            || $request->routeIs('admin.login*');
    }
}

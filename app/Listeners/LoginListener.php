<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\ActivityLogService;

class LoginListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if ($event->user) {
            ActivityLogService::log('login', 'auth', $event->user->id, null, ['ip' => request()->ip()]);
        }
    }
}

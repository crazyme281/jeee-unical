<?php

namespace App\Listeners;

use App\Notifications\NewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class UserRegistered
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * The welcome email is a side effect: if the mail server is unreachable
     * (bad MAIL_HOST, blocked SMTP port, etc.) the user must still be registered
     * and logged in, so we log the failure instead of returning a 500.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        try {
            $event->user->notify(new NewUser());
        } catch (\Throwable $e) {
            Log::error('NewUser welcome email failed: ' . $e->getMessage());
        }
    }
}
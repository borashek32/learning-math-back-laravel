<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordChangedEvent;
use App\Jobs\Auth\SendPasswordChangedEmailJob;

class SendPasswordChangedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordChangedEvent $event): void
    {
        SendPasswordChangedEmailJob::dispatch(
            $event->email
        )->onQueue('default');
    }
}

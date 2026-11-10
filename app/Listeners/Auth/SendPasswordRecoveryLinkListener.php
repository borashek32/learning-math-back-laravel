<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordRecoveryRequestedEvent;
use App\Jobs\Auth\SendPasswordRecoveryLinkJob;

class SendPasswordRecoveryLinkListener
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
    public function handle(PasswordRecoveryRequestedEvent $event): void
    {
        SendPasswordRecoveryLinkJob::dispatch(
            $event->email,
            $event->code
        )->onQueue('default');
    }
}

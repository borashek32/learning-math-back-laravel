<?php

namespace App\Listeners\Auth;

use App\Events\Auth\NewUserRegisteredEvent;
use App\Jobs\Auth\SendEmailVerificationJob;

class NewUserRegisteredListener
{
    public function handle(NewUserRegisteredEvent $userRegistered): void
    {
        SendEmailVerificationJob::dispatch($userRegistered->email)->onQueue('default');
    }
}

<?php

namespace App\Jobs\Auth;

use App\Helpers\Unisender\Dto\EmailDataDto;
use App\Helpers\Unisender\UnisenderApiService;
use App\Helpers\Unisender\UniSenderTemplates;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SendEmailVerificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(UnisenderApiService $service): void
    {
        $hash = Hash::make($this->email . Str::random(40));

        $link = config('app.frontend_url') . '/auth/verify-email?hash=' . urlencode($hash);

        $data = new EmailDataDto([
            'email' => $this->email,
            'template_id' => UniSenderTemplates::SEND_EMAIL_VERIFICATION,
            'data' => [
                'link' => $link,
            ],
        ]);

        $service->sendEmail($data);
    }
}

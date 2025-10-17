<?php

namespace App\Jobs\Auth;

use App\Helpers\UniSender\Dto\EmailDataDto;
use App\Helpers\UniSender\UniSenderApiService;
use App\Helpers\UniSender\UniSenderTemplates;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPasswordRecoveryLinkJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public string $code,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(UnisenderApiService $service): void
    {
        $data = new EmailDataDto([
            'email' => $this->email,
            'template_id' => UniSenderTemplates::SEND_PASSWORD_RECOVERY_CODE,
            'data' => [
                'code' => $this->code,
            ],
        ]);

        $service->sendEmail($data);
    }
}

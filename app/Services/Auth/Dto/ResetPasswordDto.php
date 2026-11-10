<?php

declare(strict_types=1);

namespace App\Services\Auth\Dto;

use App\Helpers\Dto\Dto;

class ResetPasswordDto extends Dto
{
    public string $password;

    public string $code;
}

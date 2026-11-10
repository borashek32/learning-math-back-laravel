<?php

namespace App\Services\Auth\Dto;

use App\Helpers\DTO\Dto;

class ResetPasswordDto extends Dto
{
    public string $password;

    public string $code;
}

<?php

namespace App\Services\Auth\Dto;

use App\Helpers\DTO\Dto;

class RecoveryPasswordDto extends Dto
{
    public string $email;
}

<?php

namespace App\Helpers\UniSender\Dto;

use App\Helpers\Dto\Dto;

class EmailDataDto extends Dto
{
    public ?string $name = null;

    public string $email;

    public string $template_id;

    public array $data = [];
}

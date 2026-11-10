<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\HttpResponses\HttpResponses;

abstract class Controller
{
    use HttpResponses;
}

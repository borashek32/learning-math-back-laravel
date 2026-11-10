<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Unisender\UnisenderServiceProvider;

return [
    AppServiceProvider::class,
    UnisenderServiceProvider::class,
];

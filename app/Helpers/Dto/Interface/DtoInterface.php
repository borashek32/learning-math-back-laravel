<?php

declare(strict_types=1);

namespace App\Helpers\Dto\Interface;

interface DtoInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = []);

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

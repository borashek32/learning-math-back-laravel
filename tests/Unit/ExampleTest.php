<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testSnakeCaseStringIsConvertedToCamelCase(): void
    {
        $this->assertSame('learningMath', Str::camel('learning_math'));
    }
}

<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums;

enum StringBackedEnum: string
{
    case one = 'one';
    case two = 'two';
}

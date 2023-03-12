<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums;

enum IntBackedEnum: int
{
    case one = 1;
    case two = 2;
}

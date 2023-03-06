<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Exceptions;

use Exception;

class IncompatibleVersionist extends Exception
{
    public static function make(): self
    {
        return new self('Snapshots must use matching versionist.');
    }
}

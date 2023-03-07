<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Exceptions;

use Exception;

class IncompatibleVersionist extends Exception
{
    public static function make(string $previousVersionist, string $currentVersionist): self
    {
        return new self("Snapshots must use matching versionists. Expected {$previousVersionist}, but received {$currentVersionist}.");
    }
}

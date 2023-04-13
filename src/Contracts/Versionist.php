<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

interface Versionist
{
    /**
     * Get first version id.
     */
    public function getFirstVersion(): string;

    /**
     * Get next version id.
     */
    public function getNextVersion(string $version): string;
}

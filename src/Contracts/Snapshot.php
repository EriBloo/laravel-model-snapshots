<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin Model
 */
interface Snapshot
{
    public function subject(): MorphTo;

    public function toModel(bool $fillExcludedAttributes = false): Model;

    public function revert(): Model;

    public function branch(): Model;

    public function fork(): Model;
}

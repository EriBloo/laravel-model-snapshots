<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Concerns;

use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshots;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasSnapshotRelations
{
    /**
     * @param  class-string  $snapshotClass
     * @return MorphSnapshots
     */
    public function morphSnapshots(string $snapshotClass): MorphSnapshots
    {
        return new MorphSnapshots($snapshotClass, $this);
    }
}

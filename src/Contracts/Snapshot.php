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

    /**
     * Returns subject model filled with snapshotted attributes.
     *
     * @param  bool  $fillExcludedAttributes if true excluded attributes will be filled with subjects current values
     */
    public function toModel(bool $fillExcludedAttributes = false): Model;

    /**
     * Reverts subject model attributes to snapshotted values.
     *
     * NOTE: this will remove snapshots created after this one.
     */
    public function revert(): Model;

    /**
     * Creates new model with duplicated snapshot history up to and including this snapshot.
     *
     * Excluded attributes will be filled with subjects current values.
     */
    public function branch(): Model;

    /**
     * Creates new model without snapshot history.
     *
     * Excluded attributes will be filled with subjects current values.
     */
    public function fork(): Model;
}

<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use EriBloo\LaravelModelSnapshots\Concerns\UsesSnapshots;
use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshotModel;
use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshotModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DocumentConsumer extends Model
{
    use UsesSnapshots;

    protected $fillable = [
        'name',
    ];

    public function documentSnapshotValues(): MorphSnapshotModels
    {
        return $this->morphSnapshotModels(Document::class);
    }

    public function documentSnapshotValue(): MorphSnapshotModel
    {
        return $this->morphSnapshotModel(Document::class);
    }

    public function documentSnapshots(): MorphToMany
    {
        return $this->morphSnapshots(Document::class);
    }
}

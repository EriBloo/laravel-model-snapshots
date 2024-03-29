<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use EriBloo\LaravelModelSnapshots\Concerns\HasSnapshotRelations;
use EriBloo\LaravelModelSnapshots\Relations\MorphSnapshotModel;
use EriBloo\LaravelModelSnapshots\Relations\MorphSnapshotModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DocumentConsumer extends Model
{
    use HasSnapshotRelations;

    protected $fillable = [
        'name',
    ];

    public function documentSnapshotValues(): MorphSnapshotModels
    {
        return $this->morphSnapshotAsModels(Document::class);
    }

    public function documentSnapshotValue(): MorphSnapshotModel
    {
        return $this->morphSnapshotAsModel(Document::class);
    }

    public function documentSnapshots(): MorphToMany
    {
        return $this->morphSnapshots(Document::class);
    }

    public function documentSnapshot(): MorphToMany
    {
        return $this->morphSnapshot(Document::class);
    }
}

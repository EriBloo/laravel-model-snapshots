<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use EriBloo\LaravelModelSnapshots\Concerns\ConnectsToSnapshots;
use EriBloo\LaravelModelSnapshots\Models\Relations\MorphSnapshotModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class TestHasSnapshotRelationsModel extends Model
{
    use ConnectsToSnapshots;

    protected $fillable = [
        'name',
    ];

    public function testCreatesSnapshotsModels(): MorphSnapshotModels
    {
        return $this->morphSnapshotModels(TestCreatesSnapshotsModel::class);
    }

    public function testCreatesSnapshots(): MorphToMany
    {
        return $this->morphSnapshots(TestCreatesSnapshotsModel::class);
    }
}

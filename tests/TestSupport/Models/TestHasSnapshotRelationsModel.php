<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use EriBloo\LaravelModelSnapshots\Concerns\HasSnapshotRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class TestHasSnapshotRelationsModel extends Model
{
    use HasSnapshotRelations;

    protected $fillable = [
        'name',
    ];

    public function testCreatesSnapshotsModels(): MorphToMany
    {
        return $this->morphSnapshots(TestCreatesSnapshotsModel::class);
    }
}

<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Concerns\HasSnapshots;
use EriBloo\LaravelModelSnapshots\SnapshotOptions;
use EriBloo\LaravelModelSnapshots\Support\Versionists\SemanticVersionist;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class DocumentWithCustomOptions extends Model
{
    use HasSnapshots;

    protected $table = 'documents';

    protected $fillable = [
        'name',
        'content',
    ];

    protected $hidden = [
        'content',
    ];

    public function getSnapshotOptions(): SnapshotOptions
    {
        return SnapshotOptions::defaults()
            ->withVersionist(new SemanticVersionist())
            ->snapshotExcept(['name'])
            ->snapshotHidden()
            ->snapshotDuplicate();
    }
}

<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use Carbon\Carbon;
use EriBloo\LaravelModelSnapshots\Concerns\HasSnapshots;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Document extends Model
{
    use HasFactory;
    use HasSnapshots;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'content',
    ];
}

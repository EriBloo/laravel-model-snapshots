<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models;

use EriBloo\LaravelModelSnapshots\Concerns\HasSnapshots;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums\IntBackedEnum;
use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Enums\StringBackedEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DocumentWithCasts extends Model
{
    use HasSnapshots;

    public $timestamps = false;

    protected $table = 'cast_documents';

    protected $fillable = [
        'date_attr',
        'array_attr',
        'int_enum_attr',
        'string_enum_attr',
        'accessor_attr',
        'mutator_attr',
        'both_attr',
    ];

    protected $casts = [
        'date_attr' => 'datetime',
        'array_attr' => 'array',
        'int_enum_attr' => IntBackedEnum::class,
        'string_enum_attr' => StringBackedEnum::class,
    ];

    public function accessorAttr(): Attribute
    {
        return Attribute::get(static fn (string $value) => "accessor {$value}");
    }

    public function mutatorAttr(): Attribute
    {
        return Attribute::set(static fn (string $value) => "mutator {$value}");
    }

    public function bothAttr(): Attribute
    {
        return Attribute::make(
            get: static fn (string $value) => "accessor {$value}",
            set: static fn (string $value) => "mutator {$value}"
        );
    }
}

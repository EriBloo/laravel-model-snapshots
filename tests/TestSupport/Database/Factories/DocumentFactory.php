<?php

declare(strict_types=1);

namespace EriBloo\LaravelModelSnapshots\Tests\TestSupport\Database\Factories;

use EriBloo\LaravelModelSnapshots\Tests\TestSupport\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @return array<string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'content' => $this->faker->sentences(8, true),
        ];
    }
}

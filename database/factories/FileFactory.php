<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    public function definition(): array
    {
        [$type, $subType] = explode('/', $this->faker->mimeType());

        return [
            'size' => $this->faker->randomNumber(5),
            'tag' => 'file',
            'mime_type' => $type,
            'mime_subtype' => $subType,
            'original_name' => $this->faker->word(),
            'path' => $this->faker->filePath(),
        ];
    }
}

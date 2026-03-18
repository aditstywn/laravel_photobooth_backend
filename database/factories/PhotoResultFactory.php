<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\PhotoResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhotoResult>
 */
class PhotoResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_id' => Photo::factory(),
            'photo_ori' => $this->faker->imageUrl(640, 480),
            'photo_order' => $this->faker->optional()->numberBetween(1, 10),
        ];
    }
}

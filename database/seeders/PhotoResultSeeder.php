<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\PhotoResult;
use Illuminate\Database\Seeder;

class PhotoResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Photo::factory()->count(10)->create()->each(function ($photo) {
            $photo->photoResults()->createMany(
                PhotoResult::factory()->count(3)->make()->toArray()
            );
        });
    }
}

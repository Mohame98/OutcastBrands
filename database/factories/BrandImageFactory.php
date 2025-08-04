<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;
use App\Models\BrandImage;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BrandImage>
 */
class BrandImageFactory extends Factory
{
    protected $model = BrandImage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'brand_id' => Brand::factory(),
            'image_path' => $this->faker->imageUrl(400, 300, 'business', true, 'brand'),
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use App\Models\Category;
use App\Models\Brand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomElement([1, 2, 3]),
            'title' => $this->faker->company,
            'sub_title' => $this->faker->catchPhrase,
            'location' => $this->faker->city,
            'website' => $this->faker->url,
            'launch_date' => $this->faker->date(),
            'description' => $this->faker->paragraph,
        ];
    }
}

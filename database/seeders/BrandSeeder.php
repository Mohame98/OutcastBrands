<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\BrandImage;


class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        // Create 100 brands using the factory
        $brands = Brand::factory()->count(100)->create();

        $brands->each(function ($brand) {
           
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $brand->categories()->attach($categories);

            $imageCount = rand(1, 4);
            BrandImage::factory($imageCount)->create([
                'brand_id' => $brand->id
            ]);
        });
    }
}

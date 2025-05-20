<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = ['Footwear', 'Accessories', 'Outerwear', 'Casual', 'Formal', 'Activewear', 'Streetwear', 'Minimalist', 'Vintage', 'Preppy', 'Seasonal', 'Luxury', 'Sustainable'];

    foreach ($categories as $name) {
      \App\Models\Category::firstOrCreate(['name' => $name]);
    }
  }
}

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
    $categories = [
      'Fashion', 'Beauty', 'Home', 'Technology', 'Sports',
      'Automotive', 'Food', 'Entertainment', 'Business', 'Software', 
      'Media', 'Services', 'Luxury'
    ];

    foreach ($categories as $name) {
      \App\Models\Category::firstOrCreate(['name' => $name]);
    }
  }
}

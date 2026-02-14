<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BrandSeeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      CategorySeeder::class,
      UserSeeder::class,
      // BrandSeeder::class,
    ]);
  }
}

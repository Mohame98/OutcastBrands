<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::updateOrCreate(
      ['email' => 'verified@example.com'],
      [
        'username' => '7878',
        'email_verified_at' => now(),
        'password' => Hash::make('Mohame9999@'),
        'remember_token' => Str::random(10),
      ]
    );

    User::updateOrCreate(
      ['email' => 'newuser@example.com'],
      [
        'username' => '4543',
        'email_verified_at' => now(),
        'password' => Hash::make('Mohame8888@'),
        'remember_token' => Str::random(10),
      ]
    );

    User::updateOrCreate(
      ['email' => 'th3kid879@gmail.com'],
      [
        'username' => 'User1234',
        'email_verified_at' => now(),
        'password' => Hash::make('Mohame7777@'),
        'remember_token' => Str::random(10),
      ]
    );
  }
}
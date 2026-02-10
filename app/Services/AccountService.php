<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AccountService
{
  public function updatePassword(User $user, string $newPassword): void
  {
    $user->update([
      'password' => Hash::make($newPassword)
    ]);
  }

  public function deleteUserAssets($user): void
  {
    if ($user->profile_image) {
      Storage::disk('public')->delete($user->profile_image);
    }
  }
}
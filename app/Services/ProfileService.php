<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileService
{
  public function updateProfileImage(User $user, ?UploadedFile $file): ?string
  {
    // Delete old image if it exists
    if ($user->profile_image) {
      Storage::disk('public')->delete($user->profile_image);
    }

    $path = $file ? $file->store('profile_images', 'public') : null;
    $user->profile_image = $path;
    $user->save();

    return $path;
  }

  public function updateField(User $user, string $field, $value): void
  {
    $user->{$field} = $value;
    $user->save();
  }

  /**
   * Normalize Instagram handle or URL into a full URL
   */
  public function updateInstagram(User $user, ?string $url): void
  {
    if (!$url) {
      $user->instagram = null;
    } else {
      // If it doesn't start with http, it's a handle or partial path
      if (!Str::startsWith($url, ['http://', 'https://'])) {
        
        $handle = ltrim($url, '@');
        $url = 'https://www.instagram.com/' . ltrim($handle, '/');
      }
      $user->instagram = $url;
    }
    $user->save();
  }
}
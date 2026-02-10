<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class InteractionService
{
  public function toggleSave(Brand $brand): array
  {
    $userId = Auth::id();
    $isSaving = !$brand->savers()->where('user_id', $userId)->exists();

    $isSaving 
      ? $brand->savers()->attach($userId) 
      : $brand->savers()->detach($userId);

    return [
      'saved' => $isSaving,
      'total_saves' => $brand->savers()->count(),
      'message' => $isSaving ? 'Saved' : 'Save Removed'
    ];
  }

  public function processVote(Brand $brand, int $value): array
  {
    $user = Auth::user();
    if (!$user) return ['action' => 'unauthorized']; 
    $existing = $brand->voters()->where('user_id', $user->id)->first();

    if ($existing) {
      if ((int) $existing->pivot->vote === $value) {
        $brand->voters()->detach($user->id);
        $action = 'removed';
      } else {
        $brand->voters()->updateExistingPivot($user->id, ['vote' => $value]);
        $action = $value === 1 ? 'upvoted' : 'downvoted';
      }
    } else {
      $brand->voters()->attach($user->id, ['vote' => $value]);
      $action = $value === 1 ? 'upvoted' : 'downvoted';
    }

    $brand->refresh();

    return [
      'action' => $action,
      'total_votes' => $brand->total_votes,
      'vote' => $action === 'removed' ? 0 : $value
    ];
  }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Traits\ApiValidator;

class UserProfileController extends Controller
{
  use ApiValidator;
  protected BrandService $brandService;

  public function __construct(BrandService $brandService)
  {
    $this->brandService = $brandService;
  }

  /**
   * Display the public profile view.
   */
  public function userProfile(User $user): View
  {
    return view('pages.profile.user-profile', [
      'user'     => $user,
      'isOwner'  => Auth::id() === $user->id,
      'sender'   => Auth::user(),
      'receiver' => $user,
    ]);
  }

  /**
   * Display the saved brands view.
   */
  public function savedBrands(): View
  {
    return view('pages.profile.user-saved-brands');
  }

  /**
   * API for fetching brands on a user's profile.
   */
  public function brandProfileApi(User $user, Request $request): JsonResponse
  {
    $validated = $this->validateJson($request, [
      'search' => "nullable|string|max:255",
      'sort'   => 'nullable|in:most popular,oldest,newest',
      'filter' => 'nullable|in:all,voted',
    ]);

    $query = Brand::query();
    if (($validated['filter'] ?? null) === 'voted') {
      $query->whereHas('voters', function ($q) use ($user) {
        $q->where('user_id', $user->id)->where('vote', '>', 0);
      });
    } else {
      $query->where('user_id', $user->id);
    }

    $query->smartSearch($validated['search'] ?? null)
          ->sortBy($validated['sort'] ?? 'newest');

    // Call the service to get paginated brand cards
    return response()->json($this->brandService->getPaginatedBrandCards($query));
  }

  /**
   * API for fetching the current user's saved brands.
   * Uses the same smart search and sort logic as the main search page.
   */
  public function savedBrandsApi(Request $request): JsonResponse
  {
    $this->authorizeJson(Auth::check(), 'Please log in to view your saved brands.');
    $user = Auth::user();

    $validated = $this->validateJson($request, [
      'search' => "nullable|string|max:255",
      'sort'   => 'nullable|in:most popular,oldest,newest',
    ]);

    // Query for brands the user has saved
    $query = Brand::query()
      ->whereHas('savers', fn($q) => $q->where('user_id', $user->id));

    $query->smartSearch($validated['search'] ?? null)
          ->sortBy($validated['sort'] ?? 'most popular');

    // Return via Service
    return response()->json($this->brandService->getPaginatedBrandCards($query));
  }
}

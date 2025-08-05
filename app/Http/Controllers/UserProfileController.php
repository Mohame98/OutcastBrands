<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Brand; 
use App\Models\User;

class UserProfileController extends Controller
{
  public function userProfile(Request $request, User $user)
  {
    $sender = Auth::user(); 
    $receiver = $user; 
    $isOwner = Auth::id() === $user->id;
    return view('pages.profile.user-profile', [
      'user' => $user,
      'isOwner' => $isOwner,
      'sender' => $sender,
      'receiver' => $receiver,
    ]);
  }

  public function brandProfileApi($userId, Request $request)
  {
    $user = User::findOrFail($userId);
    $query = Brand::query();

    $filter = $request->input('filter', 'all');

    if ($filter === 'voted') {
      $query->whereHas('voters', function ($q) use ($user) {
        $q->where('user_id', $user->id)
          ->where('vote', '>', 0);
      });
    } else {
      $query->where('user_id', $user->id);
    }

    if ($request->has('search')) {
      $searchTerm = $request->input('search');
      $query->where('title', 'like', "%{$searchTerm}%");
    }

    if ($request->has('sort')) {
      $sortBy = $request->input('sort', 'most popular');
      if ($sortBy === 'most popular') {
        $query->withSum('voters as vote_score', 'brand_votes.vote')
              ->orderByDesc('vote_score');
      } elseif ($sortBy === 'oldest') {
        $query->orderBy('created_at', 'asc');
      } else {
        $query->orderBy('created_at', 'desc');
      }
    }

    $brands = $query->with(['featuredImage', 'voters', 'views', 'savers'])
                    ->paginate(6);

    $cardsHtml = $brands->map(function ($brand) {
      $authId = auth()->id();
      $vote = $authId ? $brand->voters->firstWhere('id', $authId)?->pivot->vote : null;
      $viewCount = $brand->views->count();

      return view('components.brand-card-types.grid-brand-card', [
        'brand' => $brand,
        'vote' => $vote,
        'viewCount' => $viewCount
      ])->render();
    });

    return response()->json([
      'html_cards' => $cardsHtml,
      'has_more_brands' => $brands->hasMorePages(), 
      'current_page' => $brands->currentPage(),   
      'last_page' => $brands->lastPage()      
    ]);
  }

  public function savedBrands(Request $request)
  {
    return view('pages.profile.user-saved-brands');
  }

 public function savedBrandsApi(Request $request)
  {
    $user = auth()->user();

    if (!$user) {
      return response()->json(['error' => 'Not authenticated'], 401);
    }

    $query = Brand::query();

    $query->whereHas('savers', function ($q) use ($user) {
      $q->where('user_id', $user->id);
    });

    if ($request->has('search')) {
      $searchTerm = $request->input('search');
      $query->where('title', 'like', "%{$searchTerm}%");
    }

    if ($request->has('sort')) {
      $sortBy = $request->input('sort', 'most popular');
      if ($sortBy === 'most popular') {
        $query->withSum('voters as vote_score', 'brand_votes.vote')
          ->orderByDesc('vote_score');
      } elseif ($sortBy === 'oldest') {
        $query->orderBy('created_at', 'asc');
      } else {
        $query->orderBy('created_at', 'desc');
      }
    }

    $brands = $query->with(['featuredImage', 'voters', 'views', 'savers'])->paginate(6);

    $cardsHtml = $brands->map(function ($brand) {
      $authId = auth()->id();
      $vote = $authId ? $brand->voters->firstWhere('id', $authId)?->pivot->vote : null;
      $viewCount = $brand->views->count();

      return view('components.brand-card-types.grid-brand-card', [
        'brand' => $brand,
        'vote' => $vote,
        'viewCount' => $viewCount
      ])->render();
    });

    return response()->json([
      'html_cards' => $cardsHtml,
      'has_more_brands' => $brands->hasMorePages(), 
    ]);
  }
}

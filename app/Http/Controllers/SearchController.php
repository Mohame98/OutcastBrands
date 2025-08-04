<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand; 
use App\Models\User;
use Carbon\Carbon;

class SearchController extends Controller
{
  public function searchView()
  {
    return view("pages.search");
  }

 
  public function searchApi(Request $request)
  {

    $query = Brand::query();

    if ($request->has('search')) {
      $searchTerm = $request->input('search');
      $query->where(function ($q) use ($searchTerm) {
        $q->where('title', 'like', "%{$searchTerm}%")
          ->orWhere('location', 'like', "%{$searchTerm}%");
      });
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

    if ($request->has('category')) {
      $categories = explode(',', $request->input('category')); 
      $query->where(function ($q) use ($categories) {
        foreach ($categories as $category) {
          $q->orWhereHas('categories', function($q2) use ($category) {
            $q2->where('name', '=', trim($category)); 
          });
        }
      });
    }

    if ($request->has('filter')) {
      $filter = $request->input('filter');
      $now = Carbon::now();

      switch ($filter) {
        case 'past-week':
          $query->where('launch_date', '>=', $now->subWeek());
          break;

        case 'past-month':
          $query->where('launch_date', '>=', $now->subMonth());
          break;

        case 'past-3-months':
          $query->where('launch_date', '>=', $now->subMonths(3));
          break;

        case 'past-year':
          $query->where('launch_date', '>=', $now->subYear());
          break;
        case 'all':

        default:
          break;
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
    ]);
  }
}

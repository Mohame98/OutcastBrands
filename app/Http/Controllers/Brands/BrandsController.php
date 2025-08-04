<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\BrandImage;
use App\Models\Category;
use App\Models\BrandView;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
  public function index(Brand $brand)
  {
    $view_count = $brand->views()->count();

    $topBrands = Brand::with([
      'featuredImage' => function ($query) {
        $query->select('id', 'brand_id', 'image_path');
      }
    ])->withCount([
      'voters as total_votes' => function ($query) {
        $query->select(DB::raw('COALESCE(SUM(vote), 0)'));
      },
      'savers'
    ])
    ->orderByDesc('total_votes')
    ->take(7)
    ->get();

    $featuredBrand = $topBrands->first();
    $otherBrands = $topBrands->slice(1);

    return view('pages.home', [
      'featuredBrand' => $featuredBrand,
      'otherBrands'   => $otherBrands,
      'view_count'    => $view_count,
    ]);
  }

  public function storeBrand1(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'sub_title' => 'required|string|max:255',
      'location' => 'required|string|max:255',
      'website' => 'nullable|url|max:255',
      'launch_date' => 'nullable|date',
    ]);

    if ($validated) {
      session(['step1' => $validated]);
      return response()->json([
        'success' => true,
        'multi_step' => true,
      ]);
    } else {
      return response()->json(['errors' => $validated->errors()], 422);
    }
  }
// missing data
  public function storeBrand2(Request $request)
{
    $validated = $request->validate([
        'description' => 'nullable|string',
    ]);

    if ($validated) {
        session(['step2' => $validated]);

        return response()->json([
            'success' => true,
            'multi_step' => true,
        ]);
    } else {
        return response()->json(['errors' => $validated->errors()], 422);
    }
}

  public function storeBrand3(Request $request)
  {
    $validated = $request->validate([
      'photos' => 'required|array|min:1|max:4',
      'photos.*' => 'image|mimes:jpg,png|max:4000',
    ]);

    $maxTotalSize = 4 * 1024 * 1024;
    $totalSize = 0;

    foreach ($request->file('photos') as $photo) {
      $totalSize += $photo->getSize();
    }

     if ($totalSize > $maxTotalSize) {
      return response()->json([
        'errors' => ['photos' => ['Total size of all files must not exceed 4MB.']]
      ], 422);
    }

    $paths = [];
    foreach ($request->file('photos') as $photo) {
      $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
      $paths[] = $photo->storeAs('temp/brands', $filename, 'public');
    }


      session(['step3' => ['photos' => $paths]]);

      return response()->json([
        'success' => true,
        'multi_step' => true,
      ]);
    
  }

  public function storeBrand4(Request $request)
  {
    $validated = $request->validate([
      'categories' => 'required|array|min:1|max:3',
      'categories.*' => 'string|in:Footwear,Accessories,Outerwear,Casual,Formal,Activewear,Streetwear,Minimalist,Vintage,Preppy,Seasonal,Luxury,Sustainable',
    ]);

    if (empty($validated['categories'])) {
      return response()->json([
        'message' => 'Please select at least one category.',
        'errors' => ['categories' => ['The categories field is required.']]
      ], 422);
    }

    $step1 = session('step1');
    $step2 = session('step2');
    $step3 = session('step3');

    if (!$step1 || !$step2 || !$step3) {
      return response()->json(['error' => 'Missing data from previous steps.'], 422);
    }

    $brand = $request->user()->brands()->create([
      'user_id' => auth()->id(),
      'title' => $step1['title'],
      'sub_title' => $step1['sub_title'],      
      'location' => $step1['location'],
      'website' => $step1['website'],
      'launch_date' => $step1['launch_date'],
      'description' => $step2['description'],
    ]);

    $categoryIds = Category::whereIn('name', $validated['categories'])->pluck('id');
    $brand->categories()->sync($categoryIds);

    foreach ($step3['photos'] as $index => $path) {
      BrandImage::create([
        'brand_id' => $brand->id,
        'image_path' => $path,
        'is_featured' => $index === 0,
      ]);
    }

    session()->forget(['step1', 'step2', 'step3']);
    return response()->json([
      'success' => true,
      'message' => 'Brand posted!',
      'multi_step' => true,
    ]);
  } 

  public function vote(Request $request, Brand $brand)
  {
    $request->validate([
      'vote' => 'required|in:1,-1'
    ]);

    $user = auth()->user();
    $voteValue = (int) $request->input('vote');
    $existingVote = $brand->voters()->where('user_id', $user->id)->first();

    if ($existingVote) {
      if ((int) $existingVote->pivot->vote === $voteValue) {
        $brand->voters()->detach($user->id);

        return response()->json([
          'message' => 'Vote removed',
          'action' => 'removed',
          'vote' => 0,
          'total_votes' => $brand->total_votes
        ]);
      }

      $brand->voters()->updateExistingPivot($user->id, ['vote' => $voteValue]);

      return response()->json([
        'message' => 'Vote updated',
        'action' => $voteValue === 1 ? 'upvoted' : 'downvoted',
        'vote' => $voteValue,
        'total_votes' => $brand->total_votes
      ]);
    }

    $brand->voters()->attach($user->id, ['vote' => $voteValue]);

    return response()->json([
      'message' => 'Vote recorded',
      'action' => $voteValue === 1 ? 'upvoted' : 'downvoted',
      'vote' => $voteValue,
      'total_votes' => $brand->total_votes
    ]);
  }

 public function toggleSave(Brand $brand)
  {
    $userId = auth()->id();
    $exists = $brand->savers()->where('user_id', $userId)->exists();

    if ($exists) {
      $brand->savers()->detach($userId);
      $message = 'Unsaved';
    } else {
      $brand->savers()->attach($userId);
      $message = 'Saved';
    }

    return response()->json([
      'saved' => !$exists,
      'total_saves' => $brand->savers()->count(),
      'message' => $message,
    ]);
  }

  public function showBrand(Brand $brand)
  {
    BrandView::create([
      'brand_id' => $brand->id,
      'ip' => request()->ip(),
      'user_agent' => request()->userAgent(),
      'referrer' => request()->headers->get('referer'),
    ]);

    $brand->loadCount(['views', 'voters', 'savers'])
      ->load(['categories', 'images']);

    $relatedBrands = Brand::where('user_id', $brand->user_id)
    ->where('id', '!=', $brand->id)
    ->inRandomOrder()
    ->take(3)
    ->get();

    return view('brands.show-brand', [
      'brand' => $brand,
      'view_count' => $brand->views_count,
      'vote_count' => $brand->voters_count,
      'save_count' => $brand->savers_count,
      'relatedBrands' => $relatedBrands,
    ]);
  }
}
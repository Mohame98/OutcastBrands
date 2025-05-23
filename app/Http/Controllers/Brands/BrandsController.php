<?php

namespace App\Http\Controllers\Brands;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\BrandImage;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
  public function index()
  {
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
    ->take(5)
    ->get();

    $featuredBrand = $topBrands->first();
    $otherBrands = $topBrands->slice(1);

    return view('pages.home', [
      'featuredBrand' => $featuredBrand,
      'otherBrands'   => $otherBrands,
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
      'description' => 'nullable|string',
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

  public function storeBrand2(Request $request)
  {
    $validated = $request->validate([
      'photos' => 'required|array|min:1|max:4',
      'photos.*' => 'image|mimes:jpg,png|max:2000',
    ]);

    $paths = [];
    foreach ($request->file('photos') as $photo) {
      $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
      $paths[] = $photo->storeAs('temp/brands', $filename, 'public');
    }

    if ($validated) {
      session(['step2' => ['photos' => $paths]]);
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

    if (!$step1 || !$step2) {
      return response()->json(['error' => 'Missing data from previous steps.'], 422);
    }

    $brand = $request->user()->brands()->create([
      'user_id' => auth()->id(),
      'title' => $step1['title'],
      'sub_title' => $step1['sub_title'],      
      'location' => $step1['location'],
      'website' => $step1['website'],
      'launch_date' => $step1['launch_date'],
      'description' => $step1['description'],
    ]);

    $categoryIds = Category::whereIn('name', $validated['categories'])->pluck('id');
    $brand->categories()->sync($categoryIds);

    // Store images
    foreach ($step2['photos'] as $index => $path) {
      BrandImage::create([
        'brand_id' => $brand->id,
        'image_path' => $path,
        'is_featured' => $index === 0,
      ]);
    }

    session()->forget(['step1', 'step2']);
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
      if ($existingVote->pivot->vote === $voteValue) {
        $brand->voters()->detach($user->id);
        return response()->json([
          'message' => 'Vote removed',
          'vote' => 0,
          'total_votes' => $brand->total_votes
        ]);
      }
      $brand->voters()->updateExistingPivot($user->id, ['vote' => $voteValue]);
    } else {
      $brand->voters()->attach($user->id, ['vote' => $voteValue]);
    }

    return response()->json([
      'message' => 'Vote has been recorded',
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
      $message = 'Unsaved successfully.';
    } else {
      $brand->savers()->attach($userId);
      $message = 'Saved successfully.';
    }

    return response()->json([
      'saved' => !$exists,
      'total_saves' => $brand->savers()->count(),
      'message' => $message,
    ]);
  }

  public function showBrand(Brand $brand)
  {
    return view('brands.show-brand', [
      'brand' => $brand,
    ]);
  }
}
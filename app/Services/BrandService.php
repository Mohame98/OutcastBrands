<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\BrandImage;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Database\Query\Builder;

class BrandService
{
  /**
   * Common method to paginate brands and render their HTML cards
   */
  public function getPaginatedBrandCards($query)
  {
    $authId = auth()->id();
    $brands = $query->with(['featuredImage', 'savers'])
      ->withCount('views')
      ->with(['voters' => function($q) use ($authId) {
        $q->where('users.id', $authId);
      }])
      ->paginate(6);

    $html = $brands->map(function ($brand) {
      return View::make('components.brand-card-types.grid-brand-card', [
        'brand'     => $brand,
        'vote'      => $brand->voters->first()?->pivot?->vote,
        'viewCount' => $brand->views_count
      ])->render();
    });

    return [
      'html_cards'      => $html,
      'has_more_brands' => $brands->hasMorePages(),
      'current_page'    => $brands->currentPage(),
      'total'           => $brands->total(),
      'last_page'       => $brands->lastPage()
    ];
  }

  public function finalizeBrandCreation($user, array $steps, array $categories): Brand
  {
    return DB::transaction(function () use ($user, $steps, $categories) {
      $brand = $user->brands()->create([
        'title' => $steps[1]['title'],
        'sub_title' => $steps[1]['sub_title'],
        'location' => $steps[1]['location'],
        'website' => $steps[1]['website'] ?? null,
        'launch_date' => $steps[1]['launch_date'] ?? null,
        'description' => $steps[2]['description'] ?? null,
      ]);

      $categoryIds = Category::whereIn('name', $categories)->pluck('id');
      $brand->categories()->sync($categoryIds);

      foreach ($steps[3]['photos'] as $index => $path) {
        BrandImage::create([
          'brand_id' => $brand->id,
          'image_path' => $path,
          'is_featured' => $index === 0,
        ]);
      }

      return $brand;
    });
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BrandImage;
use App\Models\Category;
use App\Models\BrandView;
use App\Models\Comment;
use App\Models\User;

class Brand extends Model
{
  use HasFactory;
  protected $fillable = [
    'title', 'sub_title', 'location', 'website', 'launch_date', 'description',
  ];

  /**
   * Priority-based search for Title and Location
   */
  public function scopeSmartSearch(Builder $query, ?string $term): Builder
  {
    return $query->when($term, function ($q) use ($term) {
      $q->where(function ($sub) use ($term) {
        $sub->where('title', 'like', "{$term}%")
          ->orWhere('location', 'like', "{$term}%")
          ->orWhere('title', 'like', "%{$term}%")
          ->orWhere('location', 'like', "%{$term}%");
      })
      ->orderByRaw("CASE 
        WHEN title LIKE ? THEN 1
        WHEN location LIKE ? THEN 2
        WHEN title LIKE ? THEN 3
        WHEN location LIKE ? THEN 4
        ELSE 5
      END", ["{$term}%", "{$term}%", "%{$term}%", "%{$term}%"]);
    });
  }

  /**
   * Filter by category names (comma separated string)
   */
  public function scopeByCategory(Builder $query, ?string $categories): Builder
  {
    return $query->when($categories, function ($q) use ($categories) {
      $categoryArray = array_map('trim', explode(',', $categories));
      $q->whereHas('categories', fn($sub) => $sub->whereIn('name', $categoryArray));
    });
  }

  /**
   * Filter by time thresholds
   */
  public function scopeCreatedWithin(Builder $query, ?string $filter): Builder
  {
    $threshold = match($filter) {
      'past-week'     => now()->subWeek(),
      'past-month'    => now()->subMonth(),
      'past-3-months' => now()->subMonths(3),
      'past-year'     => now()->subYear(),
      default         => null
    };

    return $query->when($threshold, fn($q) => $q->where('created_at', '>=', $threshold));
  }

  /**
   * Dynamic Sorting Logic
   */
  public function scopeSortBy(Builder $query, ?string $criteria): Builder
  {
    return match ($criteria) {
      'most popular' => $query->withSum('voters as vote_score', 'brand_votes.vote')
          ->orderByDesc('vote_score'),
      'oldest'       => $query->orderBy('created_at', 'asc'),
      default        => $query->orderBy('created_at', 'desc'),
    };
  }

  public function scopePopular(Builder $query, int $limit = 10): Builder
  {
    return $query->with(['featuredImage:id,brand_id,image_path'])
      ->withSum('voters as total_votes', 'brand_votes.vote')
      ->withCount('savers')
      ->orderByDesc('total_votes')
      ->limit($limit);
  }

  public function images()
  {
    return $this->hasMany(BrandImage::class);
  }

  public function featuredImage()
  {
    return $this->hasOne(BrandImage::class)->where('is_featured', true);
  }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'brand_category');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function voters() 
  {
    return $this->belongsToMany(User::class, 'brand_votes')->withPivot('vote')->withTimestamps();
  }

  public function getTotalVotesAttribute()
  {
    return $this->voters()->sum('vote');
  }

  public function savers() 
  {
    return $this->belongsToMany(User::class, 'brand_saves')->withTimestamps();
  }

  public function views()
  {
    return $this->hasMany(BrandView::class);
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }
}

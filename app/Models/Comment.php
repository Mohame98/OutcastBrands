<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Comment extends Model
{ 
  protected $fillable = ['user_id', 'brand_id', 'comment_text'];

  /**
   * SCOPE: Optimized check to see if auth user liked the comment
   * Access in Blade via: $comment->is_liked
   */
  public function scopeWithAuthLikeStatus(Builder $query): Builder
  {
    return $query->withExists(['likes as is_liked' => function ($q) {
      $q->where('user_id', auth()->id());
    }]);
  }

  /**
   * SCOPE: Filter comments liked by a specific user
   */
  public function scopeLikedBy(Builder $query, ?User $user): Builder
  {
    return $query->when($user, function ($q) use ($user) {
      $q->whereHas('likes', fn($sub) => $sub->where('user_id', $user->id));
    });
  }

  /**
   * SCOPE: Sorting logic
   */
  public function scopeSortBy(Builder $query, ?string $criteria): Builder
  {
    return match ($criteria) {
      
      'most liked' => $query->withCount('likes')->orderByDesc('likes_count'),
      'oldest'     => $query->orderBy('created_at', 'asc'),
      default      => $query->orderBy('created_at', 'desc'), // newest
    };
  }

  protected static function booted()
  {
    static::created(function ($comment) {
      $comment->brand?->increment('comments_count');
    });

    static::deleted(function ($comment) {
      $comment->brand?->decrement('comments_count');
    });
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

    public function likes()
  {
    return $this->hasMany(CommentLike::class);
  }

  public function getLikesCountAttribute()
  {
    return $this->likes()->count();
  }

  // public function likedByAuthUser(): Attribute
  // {
  //   return Attribute::get(function () {
  //     return auth()->check() && $this->likes->contains('user_id', auth()->id());
  //   });
  // }
}

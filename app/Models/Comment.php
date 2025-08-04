<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\User;


class Comment extends Model
{
    
    protected $fillable = ['user_id', 'brand_id', 'comment_text'];

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

    protected static function booted()
    {
        static::created(function ($comment) {
            $comment->brand?->increment('comments_count');
        });

        static::deleted(function ($comment) {
            $comment->brand?->decrement('comments_count');
        });
    }

    public function likedByAuthUser(): Attribute
    {
        return Attribute::get(function () {
            return auth()->check() && $this->likes->contains('user_id', auth()->id());
        });
    }
}

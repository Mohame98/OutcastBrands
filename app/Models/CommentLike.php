<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Comment;
use App\Models\User;


class CommentLike extends Model
{
    protected $table = 'comment_likes';

    protected $fillable = ['user_id', 'comment_id'];

    public function comment()
    {
      return $this->belongsTo(Comment::class);
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
      static::created(function ($like) {
        $like->comment?->increment('likes_count');
      });

      static::deleted(function ($like) {
        $like->comment?->decrement('likes_count');
      });
    }
}

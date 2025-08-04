<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

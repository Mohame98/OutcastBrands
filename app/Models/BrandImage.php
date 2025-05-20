<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class BrandImage extends Model
{
  protected $fillable = [
    'brand_id', 'image_path', 'is_featured',
  ];

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }
}

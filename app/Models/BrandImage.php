<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Brand;

class BrandImage extends Model
{
  use HasFactory;
  protected $fillable = [
    'brand_id', 'image_path', 'is_featured',
  ];

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Category extends Model
{
  public function brands()
  {
    return $this->belongsToMany(Brand::class);
  }
}

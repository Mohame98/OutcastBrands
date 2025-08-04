<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandView extends Model
{
  protected $fillable = ['brand_id', 'ip', 'user_agent', 'referrer'];
}

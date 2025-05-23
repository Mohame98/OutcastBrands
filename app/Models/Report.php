<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
  protected $fillable = [
    'reportable_type',
    'reportable_id',
    'user_id', 
    'reason',
    'report_description',
  ];

  public function reportable()
  {
    return $this->morphTo();
  }
}

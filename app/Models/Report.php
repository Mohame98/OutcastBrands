<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Report extends Model
{
  protected $fillable = [
    'user_id',
    'reportable_type',
    'reportable_id',
    'reason',
    'report_description'
  ];

  /**
   * The MorphTo relationship allows this report to belong to
   * a Brand, a User, or a Comment.
   */
  public function reportable(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * The user who submitted the report.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * SCOPE: Filter reports by type (brand, comment, user)
   */
  public function scopeOfType(Builder $query, string $type): Builder
  {
    return $query->where('reportable_type', $type);
  }

  /**
   * SCOPE: Filter by specific reasons
   */
  public function scopeForReason(Builder $query, string $reason): Builder
  {
    return $query->where('reason', $reason);
  }
}
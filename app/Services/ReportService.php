<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReportService
{
  /**
   * Map of short names to full model classes.
   * Senior Tip: You can also use Relation::morphMap in a ServiceProvider.
   */
  protected array $types = [
    'brand'   => \App\Models\Brand::class,
    'user'    => \App\Models\User::class,
    'comment' => \App\Models\Comment::class,
  ];

  /**
   * Create a report after validating the target exists.
   */
  public function createReport(array $data): Report
  {
    $modelClass = $this->resolveModelClass($data['reportable_type']);

    // Ensure the reported item actually exists
    if (!$modelClass::where('id', $data['reportable_id'])->exists()) {
      throw new HttpResponseException(response()->json([
        'success' => false,
        'error' => 'The item you are reporting no longer exists.'
      ], 404));
    }

    return Report::create([
      'user_id'            => Auth::id(),
      'reportable_type'    => $data['reportable_type'],
      'reportable_id'      => $data['reportable_id'],
      'reason'             => $data['reason'],
      'report_description' => $data['report_description'] ?? null,
    ]);
  }

  protected function resolveModelClass(string $type): string
  {
    $type = strtolower($type);
    if (!array_key_exists($type, $this->types)) {
      throw new HttpResponseException(response()->json([
        'success' => false,
        'error' => 'Invalid report type.'
      ], 400));
    }
    return $this->types[$type];
  }
}
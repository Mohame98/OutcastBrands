<?php

namespace App\Http\Controllers;

use App\Traits\ApiValidator;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
  use ApiValidator;

  protected ReportService $reportService;

  public function __construct(ReportService $reportService)
  {
    $this->reportService = $reportService;
  }

  /**
   * Validate reason and target
   */
  public function storeReportStep1(Request $request): JsonResponse
  {
    $this->authorizeJson(Auth::check());

    $validated = $this->validateJson($request, [
      'reportable_type' => 'required|string',
      'reportable_id'   => 'required|integer',
      'reason'          => 'required|string|in:Sexual content,Violent or repulsive content,Hateful or abusive content,Harassment or bullying,Misinformation,Child abuse,Promotes terrorism,Spam or misleading,Legal issue,Captions issue',
    ]);

    session(['report_step1' => $validated]);

    return response()->json([
      'success' => true,
      'multi_step' => true,
    ]);
  }

  /**
   * Validate description and finalize
   */
  public function storeReportStep2(Request $request): JsonResponse
  {
    $this->authorizeJson(Auth::check(), 'You must be logged in to report content.');

    $validated = $this->validateJson($request, [
      'report_description' => 'nullable|string|max:500',
    ]);

    $step1 = session('report_step1');
    $this->authorizeJson(isset($step1));

    $validated['report_description'] = strip_tags($validated['report_description']);
    $reportData = array_merge($step1, $validated);
    $this->reportService->createReport($reportData);

    session()->forget('report_step1');

    return response()->json([
      'success' => true, 
      'message' => 'We’ve received your report and will review it shortly.',
      'multi_step' => true,
    ]);
  }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;


class ReportController extends Controller
{

  public function storeReportStep1(Request $request)
  {
    $validated = $request->validate([
      'reportable_type' => ['required', 'string'],
      'reportable_id' => ['required', 'integer'],
      'reason' => ['required', 'in:Sexual content,Violent or repulsive content,Hateful or abusive content,Harassment or bullying,Misinformation,Child abuse,Promotes terrorism,Spam or misleading,Legal issue,Captions issue'],
    ]);

    session(['report_step1' => $validated]);
    return response()->json([
      'success' => true,
      'multi_step' => true,
    ]);
  }

  public function storeReportStep2(Request $request)
  {
    $validated = $request->validate([
      'report_description' => ['nullable', 'string', 'max:1000'],
    ]);

    $step1 = session('report_step1');

    if (!$step1) {
      return response()->json(['error' => 'Step 1 information missing.'], 422);
    }

    $shortTypes = [
      'brand' => \App\Models\Brand::class,
      'user' => \App\Models\User::class,
      'comment' => \App\Models\Comment::class,
    ];

    $shortType = strtolower($step1['reportable_type']);

    if (!array_key_exists($shortType, $shortTypes)) {
      abort(400, 'Invalid reportable type.');
    }

    $type = $shortTypes[$shortType];

    Report::create([
      'reportable_type' => $type,
      'reportable_id' => $step1['reportable_id'],
      'user_id' => auth()->id(),
      'reason' => $step1['reason'],
      'report_description' => $validated['report_description'],
    ]);

    session()->forget('report_step1');
    return response()->json([
      'success' => true, 
      'message' => 'We’ve received your report and will review it shortly.',
      'multi_step' => true,
    ]);
  }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|in:App\\Models\\User,App\\Models\\Brand',
            'reportable_id'   => 'required|integer',
            'reason'          => 'required|in:Sexual content,Violent or repulsive content,Hateful or abusive content,Harassment or bullying,Misinformation,Child abuse,Promotes terrorism,Spam or misleading,Legal issue,Captions issue',
            'description'     => 'nullable|string|max:1000',
        ]);

        $report = new Report([
            'reporter_id' => auth()->id(),
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        $report->reportable()->associate(
            $request->reportable_type::findOrFail($request->reportable_id)
        );

        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted',
        ]);
    }
}

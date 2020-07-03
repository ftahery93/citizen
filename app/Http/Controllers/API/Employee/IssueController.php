<?php

namespace App\Http\Controllers\API\Employee;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Issue;
use App\Utility;
use Carbon\Carbon;
use App\LanguageManagement;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    public $utility;
    public function __construct(Request $request)
    {
        $this->utility = new Utility();
    }

    public function getIssues()
    {
        $issues = Issue::all();
        $pending = [];
        $approved = [];
        foreach ($issues as $issue) {
            if ($issues->status == 1) {
                $pending[] = $issue;
            } else {
                $approved[] = $issue;
            }
        }

        return response()->json([
            'pending' => $pending,
            'approved' => $approved
        ]);
    }

    public function getIssueDetails(Request $request, $id)
    {
        $issue = Issue::find($id);
        return response()->json($issue);
    }

    public function approveIssue(Request $request)
    {
        $validator = [
            'id' => 'required|integer|exists:issue',
            'approved_image' => 'required',
        ];
        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $issue = Issue::find($request->id);

        $file_data = $request->image;
        $file_name = 'approved_issue_image' . time() . '.png';

        if ($file_data != null) {
            Storage::disk('public')->put('approved_issue_images/' . $file_name, base64_decode($file_data));
        }
        $issue->update([
            'approved_image' => $file_name,
            'approved_date' => Carbon::now(),
            'employee_id' => $request->employee_id,
            'status' => 2
        ]);
        return response()->json([
            'message' => LanguageManagement::getLabel('report_approve_success', "en"),
        ]);
    }
}

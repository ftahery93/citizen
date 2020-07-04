<?php

namespace App\Http\Controllers\API\Employee;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Issue;
use App\Utility;
use Carbon\Carbon;
use App\LanguageManagement;
use App\User;
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
            if ($issue->status == 1) {
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
            'issue' => 'required',
            'issue.id' => 'required|exists:issue,id',
            'issue.file' => 'required',
        ];
        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $issue = $request->issue;
        $approvingIssue = Issue::find($issue['id']);

        // if ($request->hasFile('file')) {
        //     $icon = $request->file('file');
        //     $filename = time() . '.' . $icon->getClientOriginalExtension();
        //     $destinationPath = public_path('approved_issue_images/');
        //     $icon->move($destinationPath, $filename);
        //     $request['image'] = $filename;
        // }

        $file_data = $issue['file'];
        $file_name = 'approved_issue_image' . time() . '.png';

        if ($file_data != null) {
            Storage::disk('public')->put('approved_issue_images/' . $file_name, base64_decode($file_data));
        }

        $approvingIssue->update([
            'approved_image' => $file_name,
            'approved_date' => Carbon::now(),
            'employee_id' => $request->employee_id,
            'status' => 2
        ]);

        if ($approvingIssue->mobile) {
            $basic  = new \Nexmo\Client\Credentials\Basic('e5774e87', 'iDPhTRJRo362wcDo');
            $client = new \Nexmo\Client($basic);
            $message = $client->message()->send([
                'to' => '965' . $approvingIssue->mobile,
                'from' => 'Team-404',
                'text' => 'The issue that you reported is resolved. Thanks for reporting.'
            ]);
        }


        return response()->json([
            'message' => LanguageManagement::getLabel('report_approve_success', "en"),
        ]);
    }
}

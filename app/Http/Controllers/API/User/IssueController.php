<?php

namespace App\Http\Controllers\API\User;

use Illuminate\Http\Request;
use App\Issue;
use App\Utility;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\LanguageManagement;

class IssueController extends Controller
{

    public $utility;
    public function __construct(Request $request)
    {
        $this->utility = new Utility();
    }



    public function getIssues(Request $request)
    {
        $issues = Issue::all();
        $pending = [];
        $approved = [];
        foreach ($issues as $issue) {

            if ($issue->status == 1 && $issue->user_id == $request->user_id) {
                $pending[] = $issue;
            } else if ($issue->status == 2 && $issue->user_id == $request->user_id) {
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

        if ($issue->user_id == $request->user_id) {
            return response()->json($issue);
        } else {
            return response()->json([
                'error' => LanguageManagement::getLabel('issue_not_found', "en"),
            ], 404);
        }
    }

    public function reportIssue(Request $request)
    {
        $validator = [
            'issue' => 'required',
            'issue.file' => 'required',
            'issue.description' => 'required',
            'issue.latitude' => 'required',
            'issue.longitude' => 'required',
        ];

        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError) {
            return $checkForError;
        }

        // if ($request->hasFile('file')) {
        //     $icon = $request->file('file');
        //     $filename = time() . '.' . $icon->getClientOriginalExtension();
        //     $destinationPath = public_path('issue_images/');
        //     $icon->move($destinationPath, $filename);
        //     $request['image'] = $filename;
        // }

        $issue = $request->issue;

        $file_data = $issue['file'];
        $file_name = 'issue_image' . time() . '.png';

        if ($file_data != null) {
            Storage::disk('public')->put('issue_images/' . $file_name, base64_decode($file_data));
        }

        $issue['status'] = 1;
        $createdIssue = Issue::create([
            'description' => $issue['description'],
            'latitude' => $issue['latitude'],
            'longitude' => $issue['longitude'],
            'image' => $file_name,
            'status' => 1,
            'user_id' => $request->user_id,
            'fullname' => $issue['fullname'] == null ? "" : $issue['fullname'],
            'mobile' => $issue['mobile'] == null ? "" : $issue['mobile']
        ]);

        // $issue = Issue::create($request->all());
        return response()->json([
            'message' => LanguageManagement::getLabel('report_issue_success', 'en'),
        ]);
    }
}

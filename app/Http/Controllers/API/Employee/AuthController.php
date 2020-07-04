<?php

namespace App\Http\Controllers\API\Employee;

use App\Authentication;
use App\Employee;
use App\LanguageManagement;
use App\Http\Controllers\Controller;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Masafah API Documentation",
 *         description="Api description...",
 *     )
 */
/**
 * @SWG\SecurityScheme(
 *   securityDefinition="ApiAuthentication",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization"
 * )
 */
class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $utility;
    private $access_token;
    public function __construct(Request $request)
    {
        $this->utility = new Utility();
        $this->access_token = uniqid(base64_encode(str_random(50)));
    }

    public function login(Request $request)
    {
        $validator = [
            'user' => 'required',
            'user.email' => 'required|email',
            'user.password' => 'required'
        ];
        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $user = $request->user;
        $employee = Employee::where('email', $user['email'])->get()->first();
        if ($employee) {
            $token = '' . $employee->id . '' . $this->access_token;
            Authentication::create([
                'user_id' => $employee->id,
                'access_token' => $token,
                'type' => 2
            ]);
            if ($user['password'] == $employee->password) {
                return response()->json([
                    'employee' => $employee,
                    'access_token' => $token
                ]);
            } else {
                return response()->json([
                    'error' => LanguageManagement::getLabel('invalid_credentials', "en"),
                ], 404);
            }
        } else {
            return response()->json([
                'error' => LanguageManagement::getLabel('user_not_found', "en"),
            ], 404);
        }
    }

    public function createEmployee(Request $request)
    {
        $validator = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $employee = Employee::create([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $token = '' . $employee->id . '' . $this->access_token;
        // Authentication::create([
        //     'user_id' => $employee->id,
        //     'access_token' => $token,
        //     'type' => 2
        // ]);

        return response()->json([
            'employee' => $employee,
            'access_token' => $token
        ]);
    }
}

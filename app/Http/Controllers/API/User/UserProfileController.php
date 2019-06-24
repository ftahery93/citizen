<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\LanguageManagement;
use App\Models\Admin\User;
use App\Models\API\Authentication;
use App\Models\API\Otp;
use App\Models\API\RegisteredUser;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public $utility;
    public $language;
    public function __construct(Request $request)
    {
        $this->middleware('api');
        $this->middleware('checkAuth');
        $this->utility = new Utility();
        $this->language = $request->header('Accept-Language');
    }

    /**
     *
     * @SWG\Get(
     *         path="/~tvavisa/masafah/public/api/user/getProfile",
     *         tags={"User Profile"},
     *         operationId="getUserProfile",
     *         summary="Get User Profile",
     *          @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *        @SWG\Parameter(
     *             name="Authorization",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user access token",
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *     )
     *
     */
    public function getProfile(Request $request)
    {
        $user = RegisteredUser::find($request->user_id);
        return collect($user);
    }

    /**
     *
     * @SWG\Put(
     *         path="/~tvavisa/masafah/public/api/user/updateProfile",
     *         tags={"User Profile"},
     *         operationId="updateProfile",
     *         summary="Update User profile",
     *          @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *        @SWG\Parameter(
     *             name="Authorization",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user access token",
     *        ),
     *        @SWG\Parameter(
     *             name="Update profile body",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="fullname",
     *                  type="string",
     *                  description="Users full name",
     *                  example="Fakhruddin Tahery"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  description="User email",
     *                  example="ftahery@vavisa-kw.com"
     *              ),
     *              @SWG\Property(
     *                  property="image",
     *                  type="string",
     *                  description="Profile image base64",
     *                  example="9vjklhtyi9765/87997jhbsdfh/iutvs......"
     *              ),
     *          ),
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *        @SWG\Response(
     *             response=422,
     *             description="Unprocessable entity"
     *        ),
     *     )
     *
     */
    public function updateProfile(Request $request)
    {
        $validator = [
            'fullname' => 'required',
            'email' => 'required',
        ];

        $checkForMessages = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForMessages) {
            return $checkForMessages;
        }

        $user = RegisteredUser::find($request->user_id);
        $user->update([
            'fullname' => $request->fullname,
            'email' => $request->email,
        ]);

        if ($request->image != null) {
            $file_data = $request->image;
            $file_name = 'user_image_' . time() . '.png';

            if ($file_data != null) {
                Storage::disk('public')->put('user_images/' . $file_name, base64_decode($file_data));
                if ($user->image != null) {
                    Storage::disk('public')->delete('user_images/' . $user->image);
                }
            }
            $user->update([
                'image' => $file_name,
            ]);
        }

        return response()->json([
            'message' => LanguageManagement::getLabel('text_successUpdated', $this->language),
            'user' => collect($user),

        ]);
    }

    /**
     *
     * @SWG\Patch(
     *         path="/~tvavisa/masafah/public/api/user/changeMobileNumber",
     *         tags={"User Profile"},
     *         operationId="changeMobileNumber",
     *         summary="Change User's Mobile number",
     *          @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *        @SWG\Parameter(
     *             name="Authorization",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user access token",
     *        ),
     *        @SWG\Parameter(
     *             name="Change number body",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="mobile",
     *                  type="string",
     *                  description="User Mobile number - *(Required)",
     *                  example="66341897"
     *              ),
     *          ),
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *        @SWG\Response(
     *             response=422,
     *             description="Unprocessable entity"
     *        ),
     *        @SWG\Response(
     *             response=409,
     *             description="Mobile number already registered"
     *        ),
     *     )
     *
     */
    public function changeMobileNumber(Request $request)
    {
        $validator = [
            'mobile' => 'required',
        ];
        $checkForMessages = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForMessages) {
            return $checkForMessages;
        }

        $user = RegisteredUser::find($request->user_id);

        if ($user->mobile != $request->mobile) {
            $existingUser = RegisteredUser::where('mobile', $request->mobile)->get()->first();
            if ($existingUser == null) {

                //$existingOtp = Otp::where('mobile', $request->mobile)->get()->first();
                $generatedOtp = substr(str_shuffle("0123456789"), 0, 5);
                $otpUser = Otp::create([
                    'mobile' => $request->mobile,
                    'otp' => $generatedOtp,
                ]);

                return response()->json([
                    'otp' => $otpUser->otp,
                ]);
            } else {
                return response()->json([
                    'error' => LanguageManagement::getLabel('text_mobileNumberExist', $this->language),
                ], 409);
            }
        } else {
            return response()->json(['error' => LanguageManagement::getLabel('text_mobileNumberExist', $this->language)], 409);
        }
    }

    /**
     *
     * @SWG\Patch(
     *         path="/~tvavisa/masafah/public/api/user/updateMobileNumber",
     *         tags={"User Profile"},
     *         operationId="updateMobileNumber",
     *         summary="Update User's Mobile number",
     *          @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *        @SWG\Parameter(
     *             name="Authorization",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user access token",
     *        ),
     *        @SWG\Parameter(
     *             name="Change number body",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="mobile",
     *                  type="string",
     *                  description="User's Mobile number - *(Required)",
     *                  example="99653421"
     *              ),
     *              @SWG\Property(
     *                  property="otp",
     *                  type="string",
     *                  description="Received OTP - *(Required)",
     *                  example="46137"
     *              ),
     *              @SWG\Property(
     *                  property="type",
     *                  type="integer",
     *                  description="1-iOS, 2-Android - *(Required)",
     *                  example="46137"
     *              ),
     *          ),
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *        @SWG\Response(
     *             response=422,
     *             description="Unprocessable entity"
     *        ),
     *        @SWG\Response(
     *             response=401,
     *             description="Invalid OTP. Unauthorized"
     *        ),
     *        @SWG\Response(
     *             response=409,
     *             description="Mobile number already registered"
     *        ),
     *     )
     *
     */
    public function updateMobileNumber(Request $request)
    {
        $validator = [
            'mobile' => 'required',
            'otp' => 'required',
        ];

        $checkForMessages = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForMessages) {
            return $checkForMessages;
        }

        $user = RegisteredUser::find($request->user_id);

        $existingUser = RegisteredUser::where('mobile', $request->mobile)->get()->first();
        if ($existingUser != null) {
            return response()->json([
                'error' => LanguageManagement::getLabel('text_mobileNumberExist', $this->language),
            ], 409);
        } else {
            $existingUser = Otp::where('mobile', $request->mobile)->get()->first();
            if ($existingUser == null) {
                return response()->json([
                    'error' => LanguageManagement::getLabel('mobile_not_found', $this->language),
                ], 404);
            } else {
                if ($request->otp == $existingUser->otp) {
                    $user->update([
                        'mobile' => $request->mobile,
                    ]);
                } else {
                    return response()->json([
                        'error' => LanguageManagement::getLabel('text_wrongOTP', $this->language),
                    ], 401);
                }
            }
        }

        $accessToken = uniqid(base64_encode(str_random(50)));

        $token = '' . $user->id . '' . $user->mobile . '' . $accessToken;
        $access_token = $request->header('Authorization');
        $authenticatedUser = Authentication::where('access_token', $access_token)->get()->first();
        $authenticatedUser->update([
            'mobile' => $user->mobile,
            'access_token' => $token,
        ]);
        return response()->json([
            'message' => LanguageManagement::getLabel('text_successUpdated', $this->language),
            'user' => collect($user),
            'access_token' => $token,
        ]);
    }
}

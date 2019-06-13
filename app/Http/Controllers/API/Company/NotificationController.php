<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Utility;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public $utility;
    public $language;
    public function __construct(Request $request)
    {
        $this->middleware('checkAuth');
        $this->utility = new Utility();
        $this->language = $request->header('Accept-Language');
    }

    /**
     *
     * @SWG\Get(
     *         path="/~tvavisa/masafah/public/api/company/getNotifications",
     *         tags={"Company Notification"},
     *         operationId="getNotifications",
     *         summary="Get Company Notifications",
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
    public function getNotifications(Request $request)
    {
        
    }
}

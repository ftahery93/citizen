<?php

namespace App\Http\Controllers\API\Company;

use App\Page;
use App\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public $utility;
    public $language;
    public function __construct(Request $request)
    {
        $this->utility = new Utility();
        $this->language = $request->header('Accept-Language');
    }

    /**
     *
     * @SWG\Get(
     *         path="/company/getTermsAndConditions",
     *         tags={"Company Pages"},
     *         operationId="getTermsAndConditions",
     *         summary="Get Masafah Company's Terms and Conditions",
     *         @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *        @SWG\Parameter(
     *             name="Version",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="1.0.0",
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *     )
     *
     */
    public function getTermsAndConditions()
    {
        $page = Page::find(2);
        return response()->json([
            "terms_and_conditions" => $page->message,
        ]);
    }
}

<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\LanguageManagement;
use App\Models\API\Address;
use App\Utility;
use Illuminate\Http\Request;

class AddressController extends Controller
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
     * @SWG\Post(
     *         path="/masafah_upgrade/public/api/user/addAddress",
     *         tags={"User Address"},
     *         operationId="addAddress",
     *         summary="Add User address",
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
     *             name="Name",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Address name",
     *                  example="Home, office"
     *              ),
     *          ),
     *        ),
     *        @SWG\Parameter(
     *             name="Block",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="block",
     *                  type="string",
     *                  description="Block",
     *                  example="12, 13B"
     *              ),
     *          ),
     *        ),
     *        @SWG\Parameter(
     *             name="Street",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="street",
     *                  type="string",
     *                  description="Street",
     *                  example="12, 14A"
     *              ),
     *          ),
     *        ),
     *        @SWG\Parameter(
     *             name="Area",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="area",
     *                  type="string",
     *                  description="Area",
     *                  example="Salmiya, Sharq"
     *              ),
     *          ),
     *        ),
     *        @SWG\Parameter(
     *             name="Building",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="building",
     *                  type="string",
     *                  description="Building number",
     *                  example="14, 13Z"
     *              ),
     *          ),
     *        ),
     *        @SWG\Parameter(
     *             name="Notes",
     *             in="body",
     *             required=false,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="notes",
     *                  type="string",
     *                  description="Extra user notes",
     *                  example="Do not park vehicle infront of the gate"
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
    public function addAddress(Request $request)
    {
        $validationMessages = [
            'name' => 'required',
            'block' => 'required',
            'street' => 'required',
            'area' => 'required',
            'building' => 'required',
        ];

        $checkForError = $this->utility->checkForErrorMessages($request, $validationMessages, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $address = Address::create([
            'name' => $request->name,
            'block' => $request->block,
            'street' => $request->street,
            'area' => $request->area,
            'building' => $request->building,
            'notes' => $request->notes,
            'user_id' => $request->id,
        ]);

        return response()->json([
            'address_id' => $address->id,
        ]);
    }

    /**
     *
     * @SWG\Get(
     *         path="/masafah_upgrade/public/api/user/getAddressById/{id}",
     *         tags={"User Address"},
     *         operationId="getAddress",
     *         summary="Get User address by ID",
     *         @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *         @SWG\Parameter(
     *             name="Authorization",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user access token",
     *        ),
     *        @SWG\Parameter(
     *             name="id",
     *             in="path",
     *             description="Address ID",
     *             type="integer",
     *             required=true
     *        ),
     *        @SWG\Response(
     *             response=200,
     *             description="Successful"
     *        ),
     *         @SWG\Response(
     *             response=404,
     *             description="Address not found"
     *        ),
     *     )
     *
     */
    public function getAddressById($address_id)
    {
        $address = Address::find($address_id);
        if ($address != null) {
            return collect($address)->only('id', 'name', 'street', 'block', 'area', 'notes');
        } else {
            return response()->json([
                'error' => LanguageManagement::getLabel('no_address_found', $this->language),
            ], 404);
        }

    }

    /**
     *
     * @SWG\Get(
     *         path="/masafah_upgrade/public/api/user/getAddresses",
     *         tags={"User Address"},
     *         operationId="getAddresses",
     *         summary="Get all addresses of a user",
     *         @SWG\Parameter(
     *             name="Accept-Language",
     *             in="header",
     *             required=true,
     *             type="string",
     *             description="user prefered language",
     *        ),
     *         @SWG\Parameter(
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
    public function getAddresses(Request $request)
    {
        $addresses = Address::where('user_id', $request->id)->get();
        return collect($addresses);
    }

}

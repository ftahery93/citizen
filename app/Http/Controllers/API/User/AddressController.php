<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\API\Address;
use App\Utility;
use Illuminate\Http\Request;

class AddressController extends Controller
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

    public function addAddress(Request $request)
    {
        $validationMessages = [
            'name' => 'required',
            'block' => 'required',
            'street' => 'required',
            'area' => 'required',
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
            'notes' => $request->notes,
        ]);

        return response()->json([
            'address_id' => $address->id,
        ]);
    }

    public function getAddress(Request $request)
    {
        $validationMessages = [
            'id' => 'required',
        ];

        $checkForError = $this->utility->checkForErrorMessages($request, $validationMessages, 422);
        if ($checkForError) {
            return $checkForError;
        }

        $address = Address::find($request->id);

        return collect($address)->only('id', 'name', 'street', 'block', 'area', 'notes');
    }
}

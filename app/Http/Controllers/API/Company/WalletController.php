<?php

namespace App\Http\Controllers\API\Company;

use App\FreeDelivery;
use App\Http\Controllers\Controller;
use App\Utility;
use App\Wallet;
use App\WalletOffer;
use App\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public $utility;
    public $language;
    public function __construct(Request $request)
    {
        //$this->middleware('checkAuth');
        $this->utility = new Utility();
        $this->language = $request->header('Accept-Language');
    }

    /**
     *
     * @SWG\Post(
     *         path="/company/addToWallet",
     *         tags={"Company Wallet"},
     *         operationId="addToWallet",
     *         summary="Add to company's wallet",
     *         security={{"ApiAuthentication":{}}},
     *          @SWG\Parameter(
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
     *        @SWG\Parameter(
     *             name="Update profile body",
     *             in="body",
     *             required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="offer_id",
     *                  type="integer",
     *                  description="Wallet offer ID",
     *                  example=34
     *              ),
     *              @SWG\Property(
     *                  property="amount",
     *                  type="double",
     *                  description="Amount to be added",
     *                  example=50.00
     *              ),
     *              @SWG\Property(
     *                  property="isOffer",
     *                  type="boolean",
     *                  description="Amount added to wallet is under offers - *(Required)",
     *                  example=true
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
    public function addToWallet(Request $request)
    {
        $validator = [
            'isOffer' => 'required|boolean',
            'offer_id' => 'required_if:isOffer,true|exists:wallet_offers,id',
            'amount' => 'required_if:isOffer,false',
        ];

        $checkForError = $this->utility->checkForErrorMessages($request, $validator, 422);
        if ($checkForError != null) {
            return $checkForError;
        }

        $wallet = Wallet::where('company_id', $request->company_id)->get()->first();

        if ($request->isOffer) {
            $walletOffers = WalletOffer::find($request->offer_id);
            $freeDeliveries = FreeDelivery::where('company_id', $request->company_id)->get()->first();
            $quantity = $freeDeliveries->quantity;
            $quantity = $quantity + $walletOffers->free_deliveries;
            $freeDeliveries->update([
                'quantity' => $quantity,
            ]);

            WalletTransaction::create([
                'company_id' => $request->company_id,
                'amount' => $walletOffers->amount,
                'wallet_in' => 1,
            ]);

            $balance = $wallet->balance + $walletOffers->amount;

        } else {
            $balance = $wallet->balance + $request->amount;
            WalletTransaction::create([
                'company_id' => $request->company_id,
                'amount' => $request->amount,
                'wallet_in' => 1,
            ]);
        }

        $wallet->update([
            'balance' => $balance,
        ]);

        return response()->json([
            'id' => $wallet->id,
            'balance' => $wallet->balance,
        ]);

    }
    /*
    public function deductFromWallet(Request $request)
    {
    $wallet = Wallet::where('company_id', $request->company_id)->get()->first();
    if ($wallet != null) {
    WalletTransaction::create([
    'company_id' => $request->company_id,
    'amount' => $request->amount,
    'type' => false,
    ]);
    $balance = $wallet->balance - $request->amount;
    $wallet->update([
    'balance' => $balance,
    ]);
    }
    }
     */

    /**
     *
     * @SWG\Get(
     *         path="/company/getWalletOffers",
     *         tags={"Company Wallet"},
     *         operationId="getWalletOffers",
     *         summary="Get Wallet offers",
     *         security={{"ApiAuthentication":{}}},
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
    public function getWalletOffers()
    {
        $walletOffers = WalletOffer::all();
        return collect($walletOffers);
    }

/**
 *
 * @SWG\Get(
 *         path="/company/getWalletDetails",
 *         tags={"Company Wallet"},
 *         operationId="getWalletDetails",
 *         summary="Get Wallet Details",
 *         security={{"ApiAuthentication":{}}},
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
    public function getWalletDetails(Request $request)
    {
        $wallet = Wallet::where('company_id', $request->company_id)->get()->first();
        $walletTransactions = WalletTransaction::where('company_id', $request->company_id)->get();
        $transactionDetails = [];
        if ($walletTransactions != null) {
            foreach ($walletTransactions as $walletTransaction) {
                $transactionDetails[] = $walletTransaction;
            }
        }

        $freeDeliveries = FreeDelivery::where('company_id', $request->company_id)->get()->first();

        return response()->json([
            'wallet_balance' => $wallet->balance,
            'transactionDetails' => $transactionDetails,
            'free_deliveries' => $freeDeliveries->quantity,
        ]);
    }
}

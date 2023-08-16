<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $where = "";
        if ($request->has('crypto')) {
            $where = "cryptos.slug = '".$request->input('crypto')."'";
        } elseif ($request->has('user')) {
            $where = "offer.user_id = '".$request->input('user')."'";
        } else {
            abort(401, 'Bad Request');
        }
        $cryptos = DB::select('SELECT offer.id ,offer.price, offer.amount, offer.selling,
        offer.user_id, user.name as user_name, cryptos.name as crypto_name , cryptos.slug as crypto_id
        FROM `offers` as offer
        LEFT JOIN `users` as user
        ON offer.user_id = user.id
        LEFT JOIN cryptos
        ON offer.crypto_id = cryptos.id
        WHERE ?
        limit 10 offset ?', [
            $where,
            $request->input('page', 0) * 10,
        ]);
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $crypto, StoreOfferRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $offer)
    {
        $cryptos = DB::select("SELECT offer.id ,offer.price, offer.amount, offer.selling,
        offer.user_id, user.name as user_name, cryptos.name as crypto_name , cryptos.slug as crypto_id
        FROM `offers` as offer
        LEFT JOIN `users` as user
        ON offer.user_id = user.id
        LEFT JOIN cryptos
        ON offer.crypto_id = cryptos.id
        WHERE offer.id = ?", [
            $offer,
        ]);
        abort_unless($cryptos > 0, 404, 'Offer not found');
        return response()->json([
            'status' => 200,
            'result' => $cryptos[0]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, string $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $offer)
    {
        $insert = DB::delete("DELETE from offers where id = ?", [
            $offer,
        ]);
        abort_unless($insert > 0, 404, 'Offer not found');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

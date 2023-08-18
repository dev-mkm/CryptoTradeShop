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

    private function TradeOffer(int $crypto, int $user, int $price, int $amount, bool $selling, ?int $offer = null) {
        $sort1 = '';
        $sort2 = '';
        switch ($selling) {
            case true:
                $sort1 = '>=';
                $sort2 = 'desc';
                break;
            
            case false:
                $sort1 = '<=';
                $sort2 = 'asc';
                break;
        }
        $offers = DB::select("SELECT `id`,`price`,`amount` from `offers` where `crypto_id` = ? and `selling` = ? and price ? order by `price` ?", [
            $crypto,
            !$selling,
            $sort1.' '.$price,
            $sort2,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $crypto, Request $request)
    {
        $where = "'$crypto'";
        if ($request->has('selling')) {
            $where .= " AND offer.selling = ".$request->input('selling');
        }
        $cryptos = DB::select('SELECT offer.id ,offer.price, offer.amount, offer.selling,
        offer.user_id, user.name as user_name, cryptos.name as crypto_name , cryptos.slug as crypto_id
        FROM `offers` as offer
        LEFT JOIN `users` as user
        ON offer.user_id = user.id
        LEFT JOIN cryptos
        ON offer.crypto_id = cryptos.id
        WHERE cryptos.slug = '.$where.'
        limit 10 offset ?', [
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
        $cryptos = DB::select("SELECT `slug`,`id` from cryptos where slug = '?'", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $offer = DB::select("SELECT `id` from `offers` where `crypto_id` = ? and `user_id` = ?", [
            $cryptos[0]['id'],
            $user_id
        ]);
        abort_if(count($offer) > 0, 401, 'User already has an offer');
        $validated = $request->validated();
        return $this->TradeOffer($cryptos[0]['id'], $user_id, $validated['price'], $validated['amount'], $validated['selling']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $crypto, string $offer)
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
    public function update(UpdateOfferRequest $request, string $crypto, string $offer)
    {
        $cryptos = DB::select("SELECT `slug`,`id` from cryptos where slug = '?'", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $offers = DB::select("SELECT `id` from `offers` where `crypto_id` = ? and `id` = ?", [
            $cryptos[0]['id'],
            $offer
        ]);
        abort_unless(count($offers) > 0, 401, 'Offer not found');
        $off = $offers[0];
        $validated = $request->validated();
        return $this->TradeOffer($cryptos[0]['id'], $off['user_id'], $validated['price'], $validated['amount'], $off['selling'], $off['id']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $crypto, string $offer)
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

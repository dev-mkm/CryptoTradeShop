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
        $this->middleware('auth:sanctum', ['except' => ['index', 'show']]);
    }

    private function TradeOffer(int $crypto, int $user, int $price, int $amount, bool $selling, ?int $offer_id = null) {
        $sort1 = '';
        $sort2 = '';
        if ($selling) {
            $sort1 = '>=';
            $sort2 = 'desc';
            if(isset($offer_id)) {
                $offer = DB::select('SELECT `amount` from `offers` where `id` = ?', [$offer_id]);
                abort_unless(count($offer) > 0, 404, 'Order not found');
                $id = DB::table('crypto_transactions')->insertGetId(
                    ['in_out' => false, 'amount' => $amount - $offer[0]->amount, 'state' => 'success',
                    'user_id' => $user, 'crypto_id' => $crypto]
                );
                $insert = DB::insert("INSERT into cryptoBalance (`balance`, `user_id`, `crypto_id`, `c_t_id`) values (`balance` - ?, ?, ?, ?)", [
                    $amount - $offer[0]->amount,
                    $user,
                    $crypto,
                    $id
                ]);
                abort_unless($insert > 0, 401, 'Insufficient funds');
            } else {
                $id = DB::table('crypto_transactions')->insertGetId(
                    ['in_out' => false, 'amount' => $amount, 'state' => 'success',
                    'user_id' => $user, 'crypto_id' => $crypto]
                );
                $insert = DB::insert("INSERT into cryptoBalance (`balance`, `user_id`, `crypto_id`, `c_t_id`) values (?, ?, ?, ?)", [
                    $amount,
                    $user,
                    $crypto,
                    $id
                ]);
                abort_unless($insert > 0, 401, 'Insufficient funds');
            }
        } else {
            $sort1 = '<=';
            $sort2 = 'asc';
            if(isset($offer_id)) {
                $offer = DB::select('SELECT `amount` from `offers` where `id` = ?', [$offer_id]);
                abort_unless(count($offer) > 0, 404, 'Order not found');
                $insert = DB::update("UPDATE `users` set `balance` = `balance` - ? where `id` = ?", [
                    ($amount - $offer[0]->amount) * $price,
                    $user
                ]);
                abort_unless($insert > 0, 401, 'Insufficient funds');
            } else {
                $insert = DB::update("UPDATE `users` set `balance` = `balance` - ? where `id` = ?", [
                    $amount * $price,
                    $user
                ]);
                abort_unless($insert > 0, 401, 'Insufficient funds');
            }
        }
        $traded = 0;
        $back = 0;
        $count = 1;
        while ($amount > 0 && $count > 0) {
            if ($selling) {
                $offer = DB::select("SELECT `offers`.`id`,`offers`.`price`,`offers`.`amount`,`offers`.`user_id`,`cryptoBalance`.`balance`,`users`.`balance` as `user_balance` from `offers`
                left join `users`
                on `users`.`id` = `offers`.`user_id`
                left join (
                    SELECT user_id, MAX(`c_t_id`) as `CT`
                    FROM cryptoBalance
                    WHERE crypto_id = ?
                    GROUP BY user_id
                ) r
                on r.user_id = offers.user_id
                left join `cryptoBalance`
                on `cryptoBalance`.`user_id` = `offers`.`user_id` and `cryptoBalance`.`crypto_id` = `offers`.`crypto_id` and `cryptoBalance`.`c_t_id` = r.`CT`
                where `offers`.`crypto_id` = ? and `offers`.`selling` = ? and `offers`.`user_id` != ? and `offers`.`price` $sort1 ? order by `offers`.`price` $sort2 limit 1", [
                $crypto,
                $crypto,
                !$selling,
                $user,
                $price,
                ]);
                $count = count($offer);
                if ($count > 0) {
                    $offer = $offer[0];
                    $changeofferamount = $offer->amount;
                    if ($changeofferamount > $amount) {
                        $changeofferamount = $amount;
                    }
                    $id = DB::table('crypto_transactions')->insertGetId(
                        ['in_out' => true, 'amount' => $changeofferamount, 'state' => 'success',
                        'user_id' => $offer->user_id, 'crypto_id' => $crypto]
                    );
                    $insert = DB::insert("INSERT into cryptoBalance (`balance`, `user_id`, `crypto_id`, `c_t_id`) values (?, ?, ?, ?)", [
                        $changeofferamount + $offer->balance,
                        $offer->user_id,
                        $crypto,
                        $id
                    ]);
                    if ($insert) {
                        DB::update("UPDATE `users` set `balance` = ? where `id` = ?", [
                            $offer->user_balance + ($changeofferamount * ($price - round(($price + $offer->price) / 2))),
                            $offer->user_id
                        ]);
                        if ($changeofferamount > $amount) {
                            DB::update("UPDATE `offers` set `amount` = ? where `id` = ?", [
                                $offer->amount - $changeofferamount,
                                $offer->id
                            ]);
                        } else {
                            DB::delete("DELETE from `offers` where `id` = ?", [
                                $offer->id
                            ]);
                        }
                        $traded += $changeofferamount * round(($price + $offer->price) / 2);
                        $amount -= $changeofferamount;
                    }
                }
            } else {
                $offer = DB::select("SELECT `offers`.`id`,`offers`.`price`,`offers`.`amount`,`offers`.`user_id`,`users`.`balance` from `offers`
                left join `users`
                on `users`.`id` = `offers`.`user_id`
                where `offers`.`crypto_id` = ? and `offers`.`selling` = ? and `offers`.`user_id` != ? and `offers`.`price` $sort1 ? order by `offers`.`price` $sort2 limit 1", [
                $crypto,
                !$selling,
                $user,
                $price,
                ]);
                $count = count($offer);
                if ($count > 0) {
                    $offer = $offer[0];
                    $changeofferamount = $offer->amount;
                    if ($changeofferamount > $amount) {
                        $changeofferamount = $amount;
                    }
                    $insert = DB::update("UPDATE `users` set `balance` = ? where `id` = ?", [
                        $offer->balance + ($changeofferamount * round(($price + $offer->price) / 2)),
                        $offer->user_id
                    ]);
                    if ($insert > 0) {
                        if ($changeofferamount > $amount) {
                            DB::update("UPDATE `offers` set `amount` = ? where `id` = ?", [
                                $offer->amount - $changeofferamount,
                                $offer->id
                            ]);
                        } else {
                            DB::delete("DELETE from `offers` where `id` = ?", [
                                $offer->id
                            ]);
                        }
                        $traded += $changeofferamount;
                        $back += $changeofferamount * ($price - round(($price + $offer->price) / 2));
                        $amount -= $changeofferamount;
                    }
                }
            }
        }
        if ($selling) {
            DB::update("UPDATE `users` set `balance` = `balance` + ? where `id` = ?", [
                $traded,
                $user
            ]);
            if ($amount > 0) {
                if(isset($offer_id)) {
                    DB::update("UPDATE `offers` set `price` = ? ,`amount` = ?, `selling` = ? where `id` = ?", [
                        $price,
                        $amount,
                        $selling,
                        $offer_id,
                    ]);
                } else {
                    DB::insert("INSERT into `offers` (`user_id`, `crypto_id`, `price`, `amount`, `selling`) values (?, ?, ?, ?, ?)", [
                        $user,
                        $crypto,
                        $price,
                        $amount,
                        $selling
                    ]);
                }
            } elseif (isset($offer_id)) {
                DB::delete("DELETE from `offers` where `id` = ?", [
                    $offer_id,
                ]);
            }
        } else {
            $balance = DB::select("SELECT cryptoBalance.balance from (
                SELECT user_id, MAX(`c_t_id`) as `CT`
                FROM cryptoBalance
                WHERE crypto_id = ? and user_id = ?
                GROUP BY user_id
            ) r
            left join `cryptoBalance`
            on `cryptoBalance`.`user_id` = r.`user_id` and `cryptoBalance`.`c_t_id` = r.`CT`
            where `cryptoBalance`.crypto_id = ?", [
                $crypto,
                $user,
                $crypto,
            ]);
            $balance = isset($balance[0]->balance) ? $balance[0]->balance : 0;
            $id = DB::table('crypto_transactions')->insertGetId(
                ['in_out' => true, 'amount' => $traded, 'state' => 'success',
                'user_id' => $user, 'crypto_id' => $crypto]
            );
            DB::insert("INSERT into cryptoBalance (`balance`, `user_id`, `crypto_id`, `c_t_id`) values (?, ?, ?, ?)", [
                $traded + $balance,
                $user,
                $crypto,
                $id
            ]);
            DB::update("UPDATE `users` set `balance` = `balance` + ? where `id` = ?", [
                $back,
                $user
            ]);
            if ($amount > 0) {
                if(isset($offer_id)) {
                    DB::update("UPDATE `offers` set `price` = ? ,`amount` = ?, `selling` = ? where `id` = ?", [
                        $price,
                        $amount,
                        $selling,
                        $offer_id,
                    ]);
                } else {
                    DB::insert("INSERT into `offers` (`user_id`, `crypto_id`, `price`, `amount`, `selling`) values (?, ?, ?, ?, ?)", [
                        $user,
                        $crypto,
                        $price,
                        $amount,
                        $selling
                    ]);
                }
            } elseif (isset($offer_id)) {
                DB::delete("DELETE from `offers` where `id` = ?", [
                    $offer_id,
                ]);
            }
        }
        return response()->json([
            'status' => 200
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

    public function user(Request $request)
    {
        $cryptos = DB::select('SELECT offer.id ,offer.price, offer.amount, offer.selling,
        offer.user_id, user.name as user_name, cryptos.name as crypto_name , cryptos.slug as crypto_id
        FROM `offers` as offer
        LEFT JOIN `users` as user
        ON offer.user_id = user.id
        LEFT JOIN cryptos
        ON offer.crypto_id = cryptos.id
        WHERE offer.user_id = ?', [
            $request->user()->id,
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
        $user_id = $request->user()->id;
        $cryptos = DB::select("SELECT `slug`,`id` from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $offer = DB::select("SELECT `id` from `offers` where `crypto_id` = ? and `user_id` = ?", [
            $cryptos[0]->id,
            $user_id
        ]);
        abort_if(count($offer) > 0, 401, 'User already has an offer');
        $validated = $request->validated();
        return $this->TradeOffer($cryptos[0]->id, $user_id, $validated['price'], $validated['amount'], $validated['selling']);
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
        $cryptos = DB::select("SELECT `slug`,`id` from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $offers = DB::select("SELECT * from `offers` where `crypto_id` = ? and `id` = ?", [
            $cryptos[0]->id,
            $offer
        ]);
        abort_unless(count($offers) > 0, 401, 'Offer not found');
        $off = $offers[0];
        abort_unless($request->user()->id == $off->user_id, 403, 'Access denied');
        $validated = $request->validated();
        return $this->TradeOffer($cryptos[0]->id, $off->user_id, $validated['price'], $validated['amount'], $off->selling, $off->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request ,string $crypto, string $offer)
    {
        $cryptos = DB::select("SELECT * from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $offers = DB::select("SELECT `id`,`user_id` from `offers` where `crypto_id` = ? and `id` = ?", [
            $cryptos[0]->id,
            $offer
        ]);
        abort_unless(count($offers) > 0, 401, 'Offer not found');
        $off = $offers[0];
        abort_unless($request->user()->id == $off->user_id, 403, 'Access denied');
        $insert = DB::delete("DELETE from offers where id = ?", [
            $offer,
        ]);
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

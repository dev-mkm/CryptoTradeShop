<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\StoreCryptoTransactionRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request,string $crypto)
    {
        $cryptos = DB::select('SELECT `in_out`, `amount`, `state`, `date`
        FROM `crypto_transactions` WHERE `user_id` = ? AND `crypto_id` = ?', [
            $request->user()->id,
            $crypto
        ]);
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCryptoTransactionRequest $request, string $crypto)
    {
        $validated = $request->validated();
        $code = Str::random(10);
        $insert = DB::table('crypto_transactions')->insertGetId(
            ['in_out' => true, 'amount' => $validated['amount'], 'ct_token' => $code,
                        'user_id' => $request->user()->id, 'crypto_id' => $crypto]);
        abort_unless(isset($insert), 500, 'Server Error');
        return response()->json([
            'status' => 200,
            'result' => [
                'code' => $code,
                'id' => $insert
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $cryptoTransaction, string $crypto)
    {
        $cryptos = DB::select('SELECT `in_out`, `amount`, `state`, `date`
        FROM `crypto_transactions` WHERE `user_id` = ? AND `crypto_id` = ? AND `id` = ?', [
            $request->user()->id,
            $crypto,
            $cryptoTransaction
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Transaction not found');
        return response()->json([
            'status' => 200,
            'result' => $cryptos[0]
        ]);
    }

    public function pay(PaymentRequest $request)
    {
        $validated = $request->validated();
        $insert = DB::update("UPDATE `crypto_transactions` set `state` = ? where `id` = ? AND `user_id` = ? AND `ct_token` = ? AND `state` = 'waiting'", [
            $validated['status'],
            $validated['id'],
            $request->user()->id,
            $validated['code'],
        ]);
        abort_unless($insert > 0, 401, 'Invalid Request');
        if($validated['status'] == 'success') {
            $cryptos = DB::select('SELECT `amount`, `crypto_id`, `date`
            FROM `crypto_transactions` where `id` = ? AND `user_id` = ?', [
                $validated['id'],
                $request->user()->id,
            ]);
            DB::insert("INSERT into cryptoBalance (`balance`, `user_id`, `crypto_id`, `c_t_id`) values (`balance` + ?, ?, ?, ?)", [
                $cryptos[0]->amount,
                $request->user()->id,
                $cryptos[0]->crypto_id,
                $validated['id'],
            ]);
        }
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

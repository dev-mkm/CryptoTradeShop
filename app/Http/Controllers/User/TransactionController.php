<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\StoreTransactionRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cryptos = DB::select('SELECT `amount`, `state`, `date`
        FROM `transactions` WHERE `user_id` = ?', [
            $request->user()->id
        ]);
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $code = Str::random(10);
        $insert = DB::table('transactions')->insertGetId(
            ['amount' => $validated['amount'], 'pay_token' => $code,
                        'user_id' => $request->user()->id]);
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
    public function show(Request $request, string $transaction)
    {
        $cryptos = DB::select('SELECT `amount`, `state`, `date`
        FROM `transactions` WHERE `user_id` = ? AND `id` = ?', [
            $request->user()->id,
            $transaction
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
        $insert = DB::update("UPDATE `transactions` set `state` = ? where `id` = ? AND `user_id` = ? AND `pay_token` = ?  AND `state` = 'waiting'", [
            $validated['status'],
            $validated['id'],
            $request->user()->id,
            $validated['code'],
        ]);
        abort_unless($insert > 0, 401, 'Invalid Request');
        if($validated['status'] == 'success') {
            $cryptos = DB::select('SELECT `amount`, `state`, `date`
            FROM `transactions` where `id` = ? AND `user_id` = ?', [
                $validated['id'],
                $request->user()->id,
            ]);
            DB::update("UPDATE `users` set `balance` = `balance` + ? where `id` = ?", [
                $cryptos[0]->amount,
                $request->user()->id,
            ]);
        }
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

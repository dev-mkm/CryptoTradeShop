<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $where = "";
        if ($request->has('crypto')) {
            $where = "AND cryptos.slug = '".$request->input('crypto')."'";
        } elseif ($request->has('user')) {
            $where = "AND offer.user_id = '".$request->input('user')."'";
        } else {

        }
        $cryptos = DB::select('SELECT trade.id ,trade.price,
        cryptos.name as crypto_name , cryptos.slug as crypto_id,
        userTrade1.user_id as `buyer_id`, userTrade2.user_id as `seller_id`
        FROM `trades` as trade
        LEFT JOIN `userTrades` as userTrade1
        ON userTrade1.trade_id = trade.id
        LEFT JOIN `userTrades` as userTrade2
        ON userTrade2.trade_id = trade.id
        LEFT JOIN cryptos
        ON trade.crypto_id = cryptos.id
        WHERE userTrade1.role = \'Buyer\' AND userTrade2.role = \'Seller\' ?
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
     * Display the specified resource.
     */
    public function show(string $trade)
    {
        $cryptos = DB::select("SELECT trade.id ,trade.price,
        cryptos.name as crypto_name , cryptos.slug as crypto_id,
        userTrade1.user_id as `buyer_id`, userTrade2.user_id as `seller_id`
        FROM `trades` as trade
        LEFT JOIN `userTrades` as userTrade1
        ON userTrade1.trade_id = trade.id
        LEFT JOIN `userTrades` as userTrade2
        ON userTrade2.trade_id = trade.id
        LEFT JOIN cryptos
        ON trade.crypto_id = cryptos.id
        WHERE userTrade1.role = \'Buyer\' AND userTrade2.role = \'Seller\' AND trade.id = ?", [
            $trade,
        ]);
        abort_unless($cryptos > 0, 404, 'Trade not found');
        return response()->json([
            'status' => 200,
            'result' => $cryptos[0]
        ]);
    }
}

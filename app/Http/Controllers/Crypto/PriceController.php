<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Models\CryptoPrice;
use App\Http\Requests\StoreCryptoPriceRequest;
use App\Http\Requests\UpdateCryptoPriceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $crypto, Request $request)
    {
        $sort = "price.time, price.price";
        $group = "ORDER BY price.time DESC";
        if($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'year':
                    $sort = "YEAR(price.time) as `year`, AVG(price.price) as `price`";
                    $group = 'GROUP BY `year` ORDER BY `year` DESC';
                    break;

                case 'month':
                    $sort = "YEAR(price.time) as `year`, MONTH(price.time) as `month`, AVG(price.price) as `price`";
                    $group = 'GROUP BY `month` ORDER BY `year`, `month` DESC';
                    break;

                case 'week':
                    $sort = "YEAR(price.time) as `year`, WEEK(price.time) as `week`, AVG(price.price) as `price`";
                    $group = 'GROUP BY `week` ORDER BY `year`, `week` DESC';;
                    break;

                case 'day':
                    $sort = "YEAR(price.time) as `year`, MONTH(price.time) as `month`, DAY(price.time) as `day`, AVG(price.price) as `price`";
                    $group = 'GROUP BY `day` ORDER BY `year`, `month`, `day` DESC';
                    break;
            }
        }
        $cryptos = DB::select("SELECT $sort
        FROM `crypto_prices` as price
        LEFT JOIN cryptos
        ON price.crypto_id = cryptos.id
        WHERE cryptos.slug = ?
        $group
        limit 10 offset ?", [
            $crypto,
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
    public function store(StoreCryptoPriceRequest $request, string $crypto)
    {
        $cryptos = DB::select("SELECT `slug`,`id` from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless(count($cryptos) > 0, 404, 'Crypto not found');
        $validated = $request->validated();

        $insert = DB::insert("INSERT into `crypto_prices` (`time`, `price`, `crypto_id`) values (?, ?, ?)", [
            now(),
            $validated['price'],
            $cryptos[0]['id'],
        ]);
        abort_unless($insert, 500, 'Server Error');
        return response()->json([
            'status' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $cryptoPrice, string $crypto)
    {
        $cryptos = DB::select("SELECT price.time ,price.price
        , cryptos.name
        FROM `crypto_prices` as price
        LEFT JOIN cryptos
        ON offer.crypto_id = cryptos.id
        WHERE cryptos.slug = ? AND price.time = ?
        limit 8 offset ?", [
            $crypto,
            $cryptoPrice
        ]);
        abort_unless($cryptos > 0, 404, 'Price not found');
        return response()->json([
            'status' => 200,
            'result' => $cryptos[0]
        ]);
    }
}

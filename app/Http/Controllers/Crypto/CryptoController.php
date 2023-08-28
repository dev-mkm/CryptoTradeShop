<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoCryptoRequest;
use App\Http\Requests\StoreCryptoRequest;
use App\Http\Requests\UpdateCryptoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CryptoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cryptos = DB::select('SELECT cryptos.slug,cryptos.name,cryptos.logo,crypto_prices.price, o.offer from cryptos
        left join (
            SELECT crypto_id, MAX(`time`) as MaxTime
            FROM crypto_prices
            GROUP BY crypto_id
        ) r
        on r.crypto_id = cryptos.id
        left join crypto_prices
        ON crypto_prices.crypto_id = cryptos.id AND r.MaxTime = crypto_prices.time
        left join (
            SELECT crypto_id, MIN(`price`) as `offer`
            FROM offers
            Where `selling` = True
            GROUP BY crypto_id
        ) o
        ON o.crypto_id = cryptos.id');
        return response()->json([
            'status' => 200,
            'result' => $cryptos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCryptoRequest $request)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $validated = $request->safe()->except(['photo']);
        $file = $request->file('photo');

        $photo = $file->storePubliclyAs(
            'public/crypto', $validated['slug'].'.'.$file->extension()
        );
        $insert = DB::insert("INSERT into cryptos (`slug`, `name`, `logo`) values (?, ?, ?)", [
            $validated['slug'],
            $validated['name'],
            $photo,
        ]);
        abort_unless($insert, 500, 'Server Error');
        return response()->json([
            'status' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $crypto)
    {
        $cryptos = DB::select("SELECT cryptos.id,cryptos.slug,cryptos.name,cryptos.logo,crypto_prices.price, o.offer from cryptos
        left join (
            SELECT crypto_id, MAX(`time`) as MaxTime
            FROM crypto_prices
            GROUP BY crypto_id
        ) r
        on r.crypto_id = cryptos.id
        left join crypto_prices
        ON crypto_prices.crypto_id = cryptos.id AND r.MaxTime = crypto_prices.time
        left join (
            SELECT crypto_id, MIN(`price`) as `offer`
            FROM offers
            Where `selling` = True
            GROUP BY crypto_id
        ) o
        ON o.crypto_id = cryptos.id where `slug` = ?", [
            $crypto
        ]);
        abort_unless($cryptos > 0, 404, 'Crypto not found');
        return response()->json([
            'status' => 200,
            'result' => $cryptos[0]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCryptoRequest $request, string $crypto)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $validated = $request->validated();
        $updates = [];
        foreach ($validated as $key => $value) {
            $updates[] = "`$key` = '$value'";
        }
        $insert = DB::update("UPDATE cryptos set ".implode(',', $updates)." where slug = ?", [
            $crypto,
        ]);
        abort_unless($insert > 0, 404, 'Crypto not found');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $crypto)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $insert = DB::delete("DELETE from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless($insert > 0, 404, 'Crypto not found');
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }

    /**
     * Update the Photo.
     */
    public function photo(PhotoCryptoRequest $request, string $crypto)
    {
        abort_unless($request->user()->is_admin(), 403, 'Access denied');
        $cryptos = DB::select("SELECT `slug`,`name`,`logo` from cryptos where slug = ?", [
            $crypto,
        ]);
        abort_unless($cryptos > 0, 404, 'Crypto not found');
        $cr = $cryptos[0];
        Storage::delete($cr->logo);
        $file = $request->file('photo');
        $photo = $file->storePubliclyAs(
            'public/crypto', $cr->slug.'.'.$file->extension()
        );
        $insert = DB::update("UPDATE cryptos set `logo` = ? where slug = ?", [
            $photo,
            $crypto,
        ]);
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

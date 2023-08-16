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
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cryptos = DB::select('SELECT `slug`,`name`,`logo` from cryptos limit 8 offset ?', [
            $request->input('page', 0) * 8,
        ]);
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
        $validated = $request->safe()->except(['photo']);
        $file = $request->file('photo');

        $photo = $file->storePubliclyAs(
            'public/crypto', $validated['slug'].'.'.$file->extension()
        );
        $insert = DB::insert("INSERT into cryptos (`slug`, `name`, `logo`) values ('?', '?', '?')", [
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
        $cryptos = DB::select("SELECT `slug`,`name`,`logo` from cryptos where slug = '?'", [
            $crypto,
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
        $validated = $request->validated();
        $updates = [];
        foreach ($validated as $key => $value) {
            $updates[] = "`$key` = '$value'";
        }
        $insert = DB::update("UPDATE cryptos set ".implode(',', $updates)." where slug = '?'", [
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
    public function destroy(string $crypto)
    {
        $insert = DB::delete("DELETE from cryptos where slug = '?'", [
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
        $cryptos = DB::select("SELECT `slug`,`name`,`logo` from cryptos where slug = '?'", [
            $crypto,
        ]);
        abort_unless($cryptos > 0, 404, 'Crypto not found');
        $cr = $cryptos[0];
        Storage::delete($cr['photo']);
        $file = $request->file('photo');
        $photo = $file->storePubliclyAs(
            'public/crypto', $cr['slug'].'.'.$file->extension()
        );
        $insert = DB::update("UPDATE cryptos set `logo` = '?' where slug = '?'", [
            $photo,
            $crypto,
        ]);
        return response()->json([
            'status' => 200,
            'result' => $insert
        ]);
    }
}

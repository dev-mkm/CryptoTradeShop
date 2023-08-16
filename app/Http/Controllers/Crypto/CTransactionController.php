<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use App\Models\CryptoTransaction;
use App\Http\Requests\StoreCryptoTransactionRequest;
use App\Http\Requests\UpdateCryptoTransactionRequest;

class CTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCryptoTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CryptoTransaction $cryptoTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCryptoTransactionRequest $request, CryptoTransaction $cryptoTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CryptoTransaction $cryptoTransaction)
    {
        //
    }
}

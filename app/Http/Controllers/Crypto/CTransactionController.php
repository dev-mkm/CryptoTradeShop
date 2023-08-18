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
}

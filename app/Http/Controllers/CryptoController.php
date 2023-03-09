<?php

namespace App\Http\Controllers;

use App\Models\Crypto;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Crypto::all()->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Crypto $crypto)
    {
        return $crypto->toArray();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crypto $crypto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crypto $crypto)
    {
        //
    }
}

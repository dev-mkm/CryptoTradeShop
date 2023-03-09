<?php

namespace App\Http\Controllers;

use App\Models\Crypto;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Crypto $crypto)
    {
        return $crypto->offers()->with(['crypto', 'user'])->get()->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Crypto $crypto)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Crypto $crypto, Offer $offer)
    {
        return Offer::with(['crypto', 'user'])->find($offer['id'])->toArray();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crypto $crypto, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crypto $crypto, Offer $offer)
    {
        //
    }
}

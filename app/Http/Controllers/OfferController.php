<?php

namespace App\Http\Controllers;

use App\Models\Crypto;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $this->authorize('create', Offer::class);
        $validateRequest = Validator::make($request->all(), [
            'price' => "required|integer|min_digits:4|max_digits:6",
            'amount' => "required|integer|min:1|max_digits:5",
            'selling' => "required|boolean"
        ]);
        if($validateRequest->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Request',
                'errors' => $validateRequest->errors()
            ], 400);
        }
        $user = $request->user();
        $hasOffer = Offer::whereBelongsTo($user)->whereBelongsTo($crypto)->exists();
        if($hasOffer) {
            return response()->json([
                'status' => false,
                'message' => 'Offer for this currency already exists'
            ], 400);
        }
        $selling = $request->input('selling');
        $price = $request->input('price');
        $possibleTrade = Offer::whereBelongsTo($crypto)->where('selling', !$selling);
        switch ($selling) {
            case true:
                $possibleTrade = $possibleTrade->where('price', '>=', $price)->orderBy('price', 'desc');
                break;

            case false:
                $possibleTrade = $possibleTrade->where('price', '<=', $price)->orderBy('price', 'asc');
                break;
        }
        $amount = $request->input('amount');
        foreach ($possibleTrade->cursor() as $trade) {
            if ($amount == 0) {
                break;
            }
            $tradeamount = 0;
            if ($trade->amount >= $amount) {
                $tradeamount = $amount;
                $trade->amount -= $tradeamount;
                $amount = 0;
                $trade->amount == 0 ? $trade->delete() : $trade->save();
            } else {
                $tradeamount = $trade->amount;
                $amount -= $tradeamount;
                $trade->delete();
            }
            if ($selling) {
                $user->balance += $tradeamount * $price;
                //add Change in CryptoBakance
            } else {
                $trade->user->balance += $tradeamount * $price;
                //add Change in CryptoBakance
            }
        }
        if ($amount > 0) {
            //add money change
            $crypto->offers()->create([
                'price' => $price,
                'amount' => $amount,
                'selling' => $selling,
                'user_id' => $user->id,
            ]);
        }
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
        $this->authorize('update', $offer);
        $validateRequest = Validator::make($request->all(), [
            'price' => "required|integer|min_digits:4|max_digits:6",
            'amount' => "required|min:1|max_digits:5",
        ]);
        if($validateRequest->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Request',
                'errors' => $validateRequest->errors()
            ], 400);
        }
        $selling = $offer->selling;
        $price = $request->input('price');
        $possibleTrade = Offer::whereBelongsTo($crypto)->where('selling', !$selling);
        switch ($selling) {
            case true:
                $possibleTrade = $possibleTrade->where('price', '>=', $price);
                break;

            case false:
                $possibleTrade = $possibleTrade->where('price', '<=', $price);
                break;
        }
        if($possibleTrade->count() > 0) {

        } else {
            $offer->updateOrFail([
                'price' => $price,
                'amount' => $request->input('amount')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crypto $crypto, Offer $offer)
    {
        $this->authorize('delete', $offer);
        $offer->deleteOrFail();
        return response()->json([
            'status' => true,
            'message' => "Delete Successful",
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $this->authorize('create', Crypto::class);
        $validateRequest = Validator::make($request->all(), [
            'slug' => "required|string",
            'name' => "required|string",
            'price' => "required|integer",
            'logo' => "required|image|mimes:jpg,png,jpeg,gif,svg|max:2048",
        ]);
        if($validateRequest->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Request',
                'errors' => $validateRequest->errors()
            ], 400);
        }
        $crypto = new Crypto;
        $crypto->slug = $request->input('slug');
        $crypto->name = $request->input('name');
        $crypto->price = $request->input('price');
        $crypto->logo = $request->file('image')->store('image', 'public');
        $crypto->saveOrFail();
        return response()->json([
            'status' => true,
            'message' => "Created Successful",
        ]);
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
        $this->authorize('update', $crypto);
        $validateRequest = Validator::make($request->all(), [
            'slug' => "string",
            'name' => "string",
            'price' => "integer",
            'logo' => "image|mimes:jpg,png,jpeg,gif,svg|max:2048",
        ]);
        if($validateRequest->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Request',
                'errors' => $validateRequest->errors()
            ], 400);
        }
        $slug = $request->input('slug');
        if($slug != null) {
            $crypto->slug = $slug;
        }
        $name = $request->input('name');
        if($name != null) {
            $crypto->name = $name;
        }
        $price = $request->input('price');
        if($price != null) {
            $crypto->price = $price;
        }
        $logo = $request->file('image');
        if($logo != null) {
            $crypto->logo = $logo->store('image', 'public');
        }
        $crypto->saveOrFail();
        return response()->json([
            'status' => true,
            'message' => "Updated Successful",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crypto $crypto)
    {
        $this->authorize('delete', $crypto);
        $crypto->deleteOrFail();
        return response()->json([
            'status' => true,
            'message' => "Delete Successful",
        ]);
    }
}

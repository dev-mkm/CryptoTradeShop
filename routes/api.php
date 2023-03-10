<?php

use App\Http\Controllers\CryptoController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\TradeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('cryptos', CryptoController::class)->scoped(['crypto' => 'slug']);
Route::apiResource('cryptos.offers', OfferController::class)->scoped(['crypto' => 'slug']);
Route::apiResource('trades', TradeController::class)->only([
    'index', 'show'
])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

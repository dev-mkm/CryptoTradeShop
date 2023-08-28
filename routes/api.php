<?php

use App\Http\Controllers\Crypto\CryptoController;
use App\Http\Controllers\Crypto\CTransactionController;
use App\Http\Controllers\Crypto\OfferController;
use App\Http\Controllers\Crypto\PriceController;
use App\Http\Controllers\Crypto\TradeController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\TransactionController;
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

Route::post('/cryptos/{crypto}/photo', [CryptoController::class, 'photo'])->name('cryptos.photo');
Route::apiResource('cryptos', CryptoController::class);
Route::get('/offers', [OfferController::class, 'user'])->name('cryptos.user');
Route::apiResource('cryptos.offers', OfferController::class);
Route::apiResource('cryptos.prices', PriceController::class);
Route::apiResource('trades', TradeController::class)->only([
    'index', 'show'
])->middleware('auth:sanctum');
Route::put('/pay', [TransactionController::class, 'pay'])->name('pay');
Route::apiResource('transactions', TransactionController::class)->only([
    'index', 'store', 'show'
])->middleware('auth:sanctum');
Route::put('/cpay', [CTransactionController::class, 'pay'])->name('cpay');
Route::apiResource('cryptos.ctransactions', CTransactionController::class)->only([
    'index', 'store', 'show'
])->middleware('auth:sanctum');
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'authenticate');
    Route::post('/signup', 'store');
    Route::post('/users/{user}/role', 'role');
    Route::get('/users/{user}/crypto', 'crypto');
    Route::put('/account', 'update');
});
Route::apiResource('users', AuthController::class)->only([
    'index', 'delete'
]);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

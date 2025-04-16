<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CustomerController;



Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('products', ProductController::class);
Route::middleware('auth:sanctum')->group(function () {
    // Route::post('/orders', [OrderController::class, 'store']);
    Route::apiResource('orders', OrderController::class);
    Route::get('/user/orders', [OrderController::class, 'getUserOrders']);
});
Route::get('/customers', [CustomerController::class, 'index']);

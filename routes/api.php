<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Products\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$router->group(['prefix' => 'v1'], function () use ($router) {
    Route::post('register', [RegisterController::class, 'create']);
    Route::post('login', [LoginController::class, 'authenticate']);
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('logout', [LoginController::class, 'logout']);
        Route::group(['prefix' => 'products'], function () {
            Route::get('read',  [ProductsController::class, 'read']);
        });
        Route::group(['prefix' => 'order'], function () {
            Route::post('create',  [OrdersController::class, 'create']);
            Route::get('read',  [OrdersController::class, 'read']);
            Route::delete('cancel',  [OrdersController::class, 'delete']);
            Route::get('export',  [OrdersController::class, 'export']);
        });

    });
});
<?php

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


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth',
    'auth' => 'api'

], function ($router) {

    Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])->middleware('auth:api');
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('me', [App\Http\Controllers\AuthController::class, 'me'])->middleware('auth:api');



});

Route::group(['namespace' => 'User', 'middleware' => 'jwt.auth'], function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'json']);
});

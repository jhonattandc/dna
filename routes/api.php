<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientifyDealController;
use App\Http\Controllers\ClientifyContactController;
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

Route::middleware('clientify')->prefix('clientify')->group(function (){
    Route::post('/contact', [ClientifyContactController::class, 'store']);
    Route::post('/deal', [ClientifyDealController::class, 'store']);
});

Route::fallback(function (){
    abort(404, 'API resource not found');
});

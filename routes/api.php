<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Prosegur\Http\Controllers\AlarmsController;
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

Route::prefix('prosegur')->group(function () {
    // Alarm routes...
    Route::get('/alarms', [AlarmsController::class, 'index'])->name('prosegur.alarms.index');
});

Route::fallback(function (){
    abort(404, 'API resource not found');
});

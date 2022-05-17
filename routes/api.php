<?php

use App\Http\Controllers\FAController;
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


Route::get('/getAllDFA', [FAController::class, 'getAllDFA']);
Route::post('/DesignDFA', [FAController::class, 'DesignDFA']);
Route::get('/CheackFA', [FAController::class, 'returnCheackFA']);
Route::post('/acceptString', [FAController::class, 'returnacceptString']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvidersController;

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

Route::group(['prefix' => 'cqc'], function () {
    Route::get('/providers', [ProvidersController::class, 'index']);
    Route::get('/search/{query}', [ProvidersController::class, 'searchProviders']);

    Route::get('/providers/{uuid}', [ProvidersController::class, 'show']);
});
Route::group(['prefix' => 'local'], function () {
    Route::get('/providers', [ProvidersController::class, 'localIndex']);
});
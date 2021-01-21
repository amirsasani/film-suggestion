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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/imdb/dataset')->group(function (){
    Route::get('/download', [\App\Http\Controllers\ImdbPopulateController::class, 'download'])->name('dataset.download');
    Route::get('/populate', [\App\Http\Controllers\ImdbPopulateController::class, 'populate'])->name('dataset.populate');
    Route::get('/update', [\App\Http\Controllers\ImdbPopulateController::class, 'update'])->name('dataset.update');
});


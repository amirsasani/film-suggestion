<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);

Route::get('/', [\App\Http\Controllers\TitlesController::class, 'index'])->name('home');

Route::prefix('titles')->group(function () {
    Route::get('/', [\App\Http\Controllers\TitlesController::class, 'index'])->name('titles.index');
    Route::get('/new', [\App\Http\Controllers\TitlesController::class, 'insertForm'])->name('titles.insert.form');
    Route::post('/new', [\App\Http\Controllers\TitlesController::class, 'insert'])->name('titles.insert');
});

Route::post('suggest', [\App\Http\Controllers\SuggestionController::class, 'suggest'])->name('suggest');

Route::prefix('lists')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserListController::class, 'index'])->name('user-lists.index');
    Route::get('/new', [\App\Http\Controllers\UserListController::class, 'insertForm'])->name('user-lists.insert.form');
    Route::post('/new', [\App\Http\Controllers\UserListController::class, 'insert'])->name('user-lists.insert');
    Route::get('/{list}', [\App\Http\Controllers\UserListController::class, 'show'])->name('user-lists.show');
    Route::post('/{list}/add/{title}',
        [\App\Http\Controllers\UserListController::class, 'addTitleToList'])->name('user-list.titles.add');
    Route::post('/{list}/remove/{title}',
        [\App\Http\Controllers\UserListController::class, 'removeTitleFromList'])->name('user-list.titles.remove');
});

Route::prefix('profile')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
    Route::get('/suggestions', [\App\Http\Controllers\UserController::class, 'suggestions'])->name('user.suggestions');
    Route::get('/suggestions/{suggestion}', [\App\Http\Controllers\UserController::class, 'suggestion'])->name('user.suggestion');
});

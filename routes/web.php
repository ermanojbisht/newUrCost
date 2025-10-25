<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\SorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('sors', SorController::class);
Route::resource('items', ItemController::class);

Route::get('/demo', function () {
    return view('demo');
});

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SorController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get("/glass-demo", function () {
        return view("glass-demo");
    });
    Route::get("/demo", function () {
        return view("demo");
    });
    Route::resource("sors", SorController::class);
    Route::resource("items", ItemController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', App\Http\Controllers\UserController::class)->middleware(['permission:user-list|user-create|user-edit|user-delete']);
    Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware(['permission:role-list|role-create|role-edit|role-delete']);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class)->middleware(['permission:permission-list|permission-create|permission-edit|permission-delete']);
});

require __DIR__.'/auth.php';

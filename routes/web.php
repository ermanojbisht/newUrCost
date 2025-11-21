<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SorController;

Route::get('/', function () {
    return view('welcome');
});
require __DIR__ . '/auth.php';
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
    Route::resource("sors", SorController::class)->except(['show']);
    Route::get('sors/{sor}/{item?}', [SorController::class, 'show'])->where('item', '[0-9]+')->name('sors.show');
    Route::resource("items", ItemController::class);
    Route::resource("units", \App\Http\Controllers\UnitController::class);
    Route::resource("unit-groups", \App\Http\Controllers\UnitGroupController::class);
    Route::resource("resource-groups", \App\Http\Controllers\ResourceGroupController::class);
    Route::resource("truck-speeds", \App\Http\Controllers\TruckSpeedController::class);
    Route::resource("resource-capacity-rules", \App\Http\Controllers\ResourceCapacityRuleController::class);
    Route::resource("pol-skeletons", \App\Http\Controllers\PolSkeletonController::class);
    Route::resource("pol-rates", \App\Http\Controllers\PolRateController::class);
    Route::resource("rate-cards", \App\Http\Controllers\RateCardController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__ . '/user-management.php';

    Route::prefix('api/sors/{sor}/tree')->name('api.sors.tree.')->group(function () {
        Route::get('/', [SorController::class, 'getTreeData'])->name('data');
        Route::post('/', [SorController::class, 'createNode'])->name('create');
        Route::put('/{item}', [SorController::class, 'updateNode'])->name('update');
        Route::delete('/{item}', [SorController::class, 'deleteNode'])->name('delete');
        Route::post('/move', [SorController::class, 'moveNode'])->name('move');
        Route::get('/{item}', [SorController::class, 'getNode'])->name('node');
        Route::get('/{item}/details', [SorController::class, 'getNodeDetails'])->name('details');
        Route::put('/{item}/details', [SorController::class, 'updateNodeDetails'])->name('update_details');
    });

    Route::get('/sors/{sor}/admin', [SorController::class, 'admin'])->name('sors.admin');
    Route::get('api/sors/{sor}/items-datatable', [SorController::class, 'getDataTableData'])->name('api.sors.items-datatable');
});

Route::get('/sorCards', [SorController::class, 'sorCards'])->name('sorCards');
Route::get('/sors/{sor}/items-datatable-view', [SorController::class, 'dataTable'])->name('sors.datatable');

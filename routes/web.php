<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SorController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ItemSkeletonController;

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

    // Skeleton/Rate Analysis Page View
    Route::get('/sors/{sor}/items/{item}/ra', [App\Http\Controllers\ItemSkeletonController::class, 'showRaPage'])->name('sors.items.ra');
    Route::get('/sors/{sor}/items/{item}/skeleton', [App\Http\Controllers\ItemSkeletonController::class, 'showPage'])->name('sors.items.skeleton');
    Route::post('/sors/{sor}/items/{item}/skeleton/copy', [App\Http\Controllers\ItemSkeletonController::class, 'copySkeleton'])->name('sors.items.skeleton.copy');
    Route::post('/sors/{sor}/items/{item}/skeleton/resources/reorder', [ItemSkeletonController::class, 'reorderResources'])->name('sors.items.skeleton.resources.reorder');

    // Resource Details
    Route::get('/resources/search', [ResourceController::class, 'search'])->name('resources.search');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');

    Route::prefix('api/sors/{sor}/items/{item}/skeleton')->name('api.sors.items.skeleton.')->group(function () {
        Route::get('/', [App\Http\Controllers\ItemSkeletonController::class, 'show'])->name('show');

        //resources in skeleton
        Route::post('/resources', [App\Http\Controllers\ItemSkeletonController::class, 'addResource'])->name('resources.add');
        Route::post('/resources/reorder', [App\Http\Controllers\ItemSkeletonController::class, 'reorderResources'])->name('resources.reorder');
        Route::put('/resources/{skeleton}', [App\Http\Controllers\ItemSkeletonController::class, 'updateResource'])->name('resources.update');
        Route::delete('/resources/{skeleton}', [App\Http\Controllers\ItemSkeletonController::class, 'removeResource'])->name('resources.remove');

        //subitems in skeleton
        Route::post('/subitems', [App\Http\Controllers\ItemSkeletonController::class, 'addSubitem'])->name('subitems.add');
        Route::put('/subitems/{subitem}', [App\Http\Controllers\ItemSkeletonController::class, 'updateSubitem'])->name('subitems.update');
        Route::post('/subitems/reorder', [App\Http\Controllers\ItemSkeletonController::class, 'reorderSubitems'])->name('subitems.reorder');
        Route::delete('/subitems/{subitem}', [App\Http\Controllers\ItemSkeletonController::class, 'removeSubitem'])->name('subitems.remove');

        //Overheads in skeleton
        Route::post('/overheads', [App\Http\Controllers\ItemSkeletonController::class, 'addOverhead'])->name('overheads.add');
        Route::put('/overheads/{ohead}', [App\Http\Controllers\ItemSkeletonController::class, 'updateOverhead'])->name('overheads.update');
        Route::post('/overheads/reorder', [App\Http\Controllers\ItemSkeletonController::class, 'reorderOverheads'])->name('overheads.reorder');
        Route::delete('/overheads/{ohead}', [App\Http\Controllers\ItemSkeletonController::class, 'removeOverhead'])->name('overheads.remove');
    });

    Route::get('/sors/{sor}/admin', [SorController::class, 'admin'])->name('sors.admin');
    Route::get('api/sors/{sor}/items-datatable', [SorController::class, 'getDataTableData'])->name('api.sors.items-datatable');

    // Search APIs
    Route::get('api/sors/{sor}/items/search', [SorController::class, 'searchItems'])->name('api.sors.items.search');
    Route::get('api/sors/{sor}/overheads/search', [SorController::class, 'searchOverheads'])->name('api.sors.overheads.search');
});

Route::get('/sorCards', [SorController::class, 'sorCards'])->name('sorCards');
Route::get('/sors/{sor}/items-datatable-view', [SorController::class, 'dataTable'])->name('sors.datatable');

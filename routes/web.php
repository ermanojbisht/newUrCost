<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemSkeletonController;
use App\Http\Controllers\LaborIndexController;
use App\Http\Controllers\MachineIndexController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
require __DIR__ . '/auth.php';
require __DIR__.'/debug.php';
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
    Route::resource("units", \App\Http\Controllers\UnitController::class);
    Route::resource("unit-groups", \App\Http\Controllers\UnitGroupController::class);
    Route::resource("resource-groups", \App\Http\Controllers\ResourceGroupController::class);
    Route::resource("truck-speeds", \App\Http\Controllers\TruckSpeedController::class);
    Route::resource("resource-capacity-rules", \App\Http\Controllers\ResourceCapacityRuleController::class);
    Route::resource("pol-skeletons", \App\Http\Controllers\PolSkeletonController::class);
    Route::resource("pol-rates", \App\Http\Controllers\PolRateController::class);
    Route::resource("rate-cards", \App\Http\Controllers\RateCardController::class);
    Route::get('/labor-report', [\App\Http\Controllers\RateCardController::class, 'laborRateReport'])->name('rate-cards.labor-report');
    Route::get('/labor-report/pdf', [\App\Http\Controllers\RateCardController::class, 'exportLaborPdf'])->name('rate-cards.labor-report.pdf');
    Route::get('/machine-report', [\App\Http\Controllers\RateCardController::class, 'machineRateReport'])->name('rate-cards.machine-report');
    Route::get('/machine-report/pdf', [\App\Http\Controllers\RateCardController::class, 'exportMachinePdf'])->name('rate-cards.machine-report.pdf');
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

    Route::get('/sors/{sor}/items/{item}/skeleton', [App\Http\Controllers\ItemSkeletonController::class, 'showPage'])->name('sors.items.skeleton');
    Route::post('/sors/{sor}/items/{item}/skeleton/copy', [App\Http\Controllers\ItemSkeletonController::class, 'copySkeleton'])->name('sors.items.skeleton.copy');
    Route::post('/sors/{sor}/items/{item}/skeleton/resources/reorder', [ItemSkeletonController::class, 'reorderResources'])->name('sors.items.skeleton.resources.reorder');

    // Resource Details
    Route::get('/resources/search', [ResourceController::class, 'search'])->name('resources.search');
    Route::resource('resources', ResourceController::class);
    // Labor Indices
    Route::get('labor-indices/all', [LaborIndexController::class, 'allIndices'])->name('labor-indices.all');
    Route::get('labor-indices/global', [LaborIndexController::class, 'globalIndex'])->name('labor-indices.global.index');
    Route::post('labor-indices/global', [LaborIndexController::class, 'globalStore'])->name('labor-indices.global.store');
    Route::post('labor-indices/{id}/lock', [LaborIndexController::class, 'lock'])->name('labor-indices.lock');
    Route::resource('resources.labor-indices', LaborIndexController::class)->shallow();

    // Machine Indices
    Route::get('machine-indices/all', [MachineIndexController::class, 'allIndices'])->name('machine-indices.all');
    Route::get('machine-indices/global', [MachineIndexController::class, 'globalIndex'])->name('machine-indices.global.index');
    Route::post('machine-indices/global', [MachineIndexController::class, 'globalStore'])->name('machine-indices.global.store');
    Route::post('machine-indices/{id}/lock', [MachineIndexController::class, 'lock'])->name('machine-indices.lock');
    Route::resource('resources.machine-indices', MachineIndexController::class)->shallow();
    
    // Stations
    Route::post('stations/{id}/sync', [\App\Http\Controllers\StationController::class, 'sync'])->name('stations.sync');
    Route::resource('stations', \App\Http\Controllers\StationController::class);

    // Lead Distances
    Route::get('lead-distances/all', [\App\Http\Controllers\LeadDistanceController::class, 'allIndices'])->name('lead-distances.all');
    Route::post('lead-distances/{id}/lock', [\App\Http\Controllers\LeadDistanceController::class, 'lock'])->name('lead-distances.lock');
    Route::resource('resources.lead-distances', \App\Http\Controllers\LeadDistanceController::class)->shallow();

    // Item Technical Specs
    Route::post('items/{item}/generate-specs', [\App\Http\Controllers\ItemTechnicalSpecController::class, 'generate'])->name('items.generate-specs');
    Route::get('items/{item}/specs/edit', [\App\Http\Controllers\ItemTechnicalSpecController::class, 'edit'])->name('items.edit-specs');
    Route::put('items/{item}/specs', [\App\Http\Controllers\ItemTechnicalSpecController::class, 'update'])->name('items.update-specs');

    Route::prefix('resources/{resource}/rates')->name('resources.rates.')->group(function () {
        Route::get('/manage', [App\Http\Controllers\ResourceRateController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ResourceRateController::class, 'store'])->name('store');
        Route::put('/{rate}', [App\Http\Controllers\ResourceRateController::class, 'update'])->name('update');
        Route::post('/{rate}/lock', [App\Http\Controllers\ResourceRateController::class, 'lock'])->name('lock');
        Route::delete('/{rate}', [App\Http\Controllers\ResourceRateController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('api/sors/{sor}/items/{item}/skeleton')->name('api.sors.items.skeleton.')->group(function () {


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


    // Search APIs
    Route::get('api/sors/{sor}/items/search', [SorController::class, 'searchItems'])->name('api.sors.items.search');
    Route::get('api/sors/{sor}/overheads/search', [SorController::class, 'searchOverheads'])->name('api.sors.overheads.search');

    // Rate Calculation Admin
    Route::prefix('admin/rate-calculation')->name('admin.rate-calculation.')->group(function () {
        Route::get('/', [App\Http\Controllers\RateCalculationController::class, 'index'])->name('index');
        Route::post('/calculate', [App\Http\Controllers\RateCalculationController::class, 'calculate'])->name('calculate');
    });
});
Route::get('sors/{sor}/{item?}', [SorController::class, 'show'])->where('item', '[0-9]+')->name('sors.show');
Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');

Route::prefix('api/sors/{sor}/items/{item}/skeleton')->name('api.sors.items.skeleton.')->group(function () {
    Route::get('/', [App\Http\Controllers\ItemSkeletonController::class, 'show'])->name('show');
});

Route::get('/ra/{item_code}', [App\Http\Controllers\ItemSkeletonController::class, 'redirectToRa'])->name('ra.redirect');
Route::get('/sors/{sor}/items/{item}/ra', [App\Http\Controllers\ItemSkeletonController::class, 'showRaPage'])->name('sors.items.ra');
Route::get('/sors/{sor}/items/{item}/consumption', [App\Http\Controllers\ItemRateController::class, 'consumption'])->name('sors.items.consumption');
Route::get('/sors/{sor}/items/{item}/consumptionWithoutOh', [App\Http\Controllers\ItemRateController::class, 'consumptionWithoutOh'])->name('sors.items.consumptionWithoutOh');
Route::get('/sors/{sor}/items/{item}/export', [App\Http\Controllers\ItemRateController::class, 'export'])->name('sors.items.export');
Route::get('api/sors/{sor}/items-datatable', [SorController::class, 'getDataTableData'])->name('api.sors.items-datatable');

Route::get('/sorCards', [SorController::class, 'sorCards'])->name('sorCards');
Route::get('/sors/{sor}/items-datatable-view', [SorController::class, 'dataTable'])->name('sors.datatable');

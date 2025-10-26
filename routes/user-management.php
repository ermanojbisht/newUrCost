<?php

use Illuminate\Support\Facades\Route;

Route::resource('users', App\Http\Controllers\UserManagement\UserController::class)->middleware(['permission:user-list|user-create|user-edit|user-delete']);
Route::resource('roles', App\Http\Controllers\UserManagement\RoleController::class)->middleware(['permission:role-list|role-create|role-edit|role-delete']);
Route::resource('permissions', App\Http\Controllers\UserManagement\PermissionController::class)->middleware(['permission:permission-list|permission-create|permission-edit|permission-delete']);

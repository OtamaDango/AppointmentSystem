<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\AdminController;

// ----------------- Admin Routes -----------------
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminController::class,'login']); 
    Route::post('logout', [AdminController::class,'logout'])->middleware('auth:admin'); 
});

// ----------------- Officer Routes (admin only) -----------------
Route::middleware('auth:admin')->prefix('officers')->group(function () {
    Route::get('/', [OfficerController::class,'index']);
    Route::post('/', [OfficerController::class,'store']);
    Route::put('/{id}', [OfficerController::class,'update']);
    Route::post('/{id}/activate', [OfficerController::class,'activate']); 
    Route::post('/{id}/deactivate', [OfficerController::class,'deactivate']);
});
// ----------------- Officer Auth Routes -----------------
Route::prefix('officer')->group(function() {
    Route::post('login', [OfficerController::class, 'login']);
    Route::post('logout', [OfficerController::class, 'logout'])->middleware('auth:officer');
});

// ----------------- Visitor Routes -----------------
Route::middleware(['auth:admin','auth:officer'])->group(function () {
    Route::apiResource('visitors', VisitorController::class)->only(['index', 'show']);
    Route::get('visitors/{id}/appointments', [VisitorController::class,'appointments']);
});

// ----------------- Appointment Routes -----------------
Route::middleware(['auth:admin','auth:officer'])->group(function () {
    Route::apiResource('appointments', AppointmentController::class);
    Route::post('appointments/{id}/cancel', [AppointmentController::class,'cancel']);
});

// ----------------- Activity Routes -----------------
Route::middleware('auth:admin')->group(function () {
    Route::apiResource('activities', ActivityController::class);
});

// ----------------- Post Routes -----------------
Route::middleware('auth:admin')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::post('posts/{id}/activate', [PostController::class,'activate']);
    Route::post('posts/{id}/deactivate', [PostController::class,'deactivate']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\VisitorController;

// ----------------- Dashboard -----------------
Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

// ----------------- Officer Routes -----------------
Route::prefix('officers')->group(function () {
    Route::get('/', [OfficerController::class, 'index'])->name('officers.index');   // Blade: list all officers
    Route::post('/', [OfficerController::class, 'store'])->name('officers.store');   // Store new officer
    Route::get('/create', [OfficerController::class, 'create'])->name('officers.create'); // Create form

    Route::get('/{id}/edit', [OfficerController::class, 'edit'])->name('officers.edit'); // Edit form
    Route::put('/{id}', [OfficerController::class, 'update'])->name('officers.update'); // Update officer
    Route::post('/{id}/activate', [OfficerController::class, 'activate'])->name('officers.activate'); // Activate
    Route::post('/{id}/deactivate', [OfficerController::class, 'deactivate'])->name('officers.deactivate'); // Deactivate
});

// ----------------- Visitor Routes -----------------
Route::prefix('visitors')->group(function () {
    Route::get('/', [VisitorController::class, 'index'])->name('visitors.index'); // List visitors
    Route::get('/create', [VisitorController::class, 'create'])->name('visitors.create'); // Create form
    Route::post('/', [VisitorController::class, 'store'])->name('visitors.store'); // Store new visitor
    Route::get('/{id}/edit', [VisitorController::class, 'edit'])->name('visitors.edit'); // Edit form
    Route::put('/{id}', [VisitorController::class, 'update'])->name('visitors.update'); // Update visitor
    Route::post('/{id}/activate', [VisitorController::class, 'activate'])->name('visitors.activate'); // Activate
    Route::post('/{id}/deactivate', [VisitorController::class, 'deactivate'])->name('visitors.deactivate'); // Deactivate
    Route::get('/{id}/appointments', [VisitorController::class, 'viewAppointments'])->name('visitors.appointments'); // Appointments
});

// ----------------- Appointment Routes -----------------
Route::prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index'); // List appointments
    Route::post('/', [AppointmentController::class, 'store'])->name('appointments.store'); // Add appointment
    Route::get('/create', [AppointmentController::class, 'create'])->name('appointments.create'); // Create form
    Route::get('/{id}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit'); // Edit form
    Route::put('/{id}', [AppointmentController::class, 'update'])->name('appointments.update'); // Update appointment
    Route::post('/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel'); // Cancel
    Route::get('/{id}', [AppointmentController::class, 'show'])->name('appointments.show'); // Show single appointment
});

// ----------------- Activity Routes -----------------
Route::prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/{id}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');  
    Route::get('/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/', [ActivityController::class, 'store'])->name('activities.store');
    // ----------------- Activity Routes -----------------
// ----------------- Activity Routes -----------------
Route::prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/{id}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');  
    Route::get('/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/', [ActivityController::class, 'store'])->name('activities.store');
    Route::put('/{id}', [ActivityController::class, 'update'])->name('activities.update'); 
});
});

// ----------------- Post Routes -----------------
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index'); // List posts
    Route::post('/', [PostController::class, 'store'])->name('posts.store'); // Add post
    Route::get('/{id}/edit', [PostController::class, 'edit'])->name('posts.edit'); // Edit form
    Route::put('/{id}', [PostController::class, 'update'])->name('posts.update'); // Update post
    Route::get('/create', [PostController::class, 'create'])->name('posts.create'); // Create form
    Route::post('/{id}/activate', [PostController::class, 'activate'])->name('posts.activate'); // Activate post
    Route::post('/{id}/deactivate', [PostController::class, 'deactivate'])->name('posts.deactivate'); // Deactivate post
});
?>
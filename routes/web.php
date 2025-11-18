<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BusinessHourController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rota para ver e salvar horários
Route::get('/horarios', [BusinessHourController::class, 'index'])->name('business_hours.index');
Route::post('/horarios', [BusinessHourController::class, 'update'])->name('business_hours.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('appointments', AppointmentController::class);
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
});

Route::middleware(['auth', 'super.admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [App\Http\Controllers\SuperAdminController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SuperAdminController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\SuperAdminController::class, 'store'])->name('store');
    Route::get('/{business}/edit', [App\Http\Controllers\SuperAdminController::class, 'edit'])->name('edit');
    Route::put('/{business}', [App\Http\Controllers\SuperAdminController::class, 'update'])->name('update');
    Route::delete('/{business}', [App\Http\Controllers\SuperAdminController::class, 'destroy'])->name('destroy');
});

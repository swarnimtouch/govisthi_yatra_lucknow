<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminRegistrationController;

Route::get('/', [EventRegistrationController::class, 'index'])->name('event.index');
Route::post('/register', [EventRegistrationController::class, 'store'])->name('event.store');
Route::get('/result/{id}', [EventRegistrationController::class, 'result'])->name('event.result');
Route::get('/admin/registrations/{id}/download-banner', [EventRegistrationController::class, 'downloadBanner'])
    ->name('admin.registrations.download-banner');

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('logout',[AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {

        Route::get('dashboard', [AdminRegistrationController::class, 'dashboard'])->name('dashboard');

        Route::get('registrations',          [AdminRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('registrations/export',   [AdminRegistrationController::class, 'export'])->name('registrations.export');
        Route::get('registrations/{registration}',    [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::delete('registrations/{registration}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');

    });
});

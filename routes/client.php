<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvitationController;
use App\Http\Controllers\Client\GuestController;
use App\Http\Controllers\Client\PhotoController;
use App\Http\Controllers\Client\CheckoutController;
use Illuminate\Support\Facades\Route;

// Client Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Invitation Management
Route::resource('invitations', InvitationController::class);
Route::patch('/invitations/{invitation}/submit', [InvitationController::class, 'submit'])->name('invitations.submit');

// Guest Management (nested under invitations)
Route::get('/invitations/{invitation}/guests', [GuestController::class, 'index'])->name('invitations.guests.index');
Route::post('/invitations/{invitation}/guests', [GuestController::class, 'store'])->name('invitations.guests.store');
Route::delete('/invitations/{invitation}/guests/{guest}', [GuestController::class, 'destroy'])->name('invitations.guests.destroy');

// Photo Management (nested under invitations)
Route::post('/invitations/{invitation}/photos', [PhotoController::class, 'store'])->name('invitations.photos.store');
Route::delete('/invitations/{invitation}/photos/{photo}', [PhotoController::class, 'destroy'])->name('invitations.photos.destroy');

// Checkout & Payment
Route::get('/checkout/{invitation}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{invitation}/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/{invitation}/status', [CheckoutController::class, 'status'])->name('checkout.status');

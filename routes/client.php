<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvitationController;
use App\Http\Controllers\Client\GuestController;
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

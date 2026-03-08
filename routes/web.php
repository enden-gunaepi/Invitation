<?php

use App\Http\Controllers\InvitationPublicController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Role-based dashboard redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'client' => redirect('/client/dashboard'),
        default => redirect('/'),
    };
})->middleware(['auth'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public invitation routes
Route::middleware(['track.view'])->group(function () {
    Route::get('/inv/{slug}', [InvitationPublicController::class, 'show'])->name('invitation.show');
    Route::get('/inv/{slug}/{token}', [InvitationPublicController::class, 'showGuest'])->name('invitation.guest');
});
Route::post('/inv/{slug}/rsvp', [InvitationPublicController::class, 'rsvp'])->name('invitation.rsvp');
Route::post('/inv/{slug}/wish', [InvitationPublicController::class, 'wish'])->name('invitation.wish');

// Payment Gateway Callbacks (no auth - called by gateways)
Route::post('/callback/xendit', [PaymentCallbackController::class, 'xenditCallback'])->name('callback.xendit');
Route::post('/callback/tripay', [PaymentCallbackController::class, 'tripayCallback'])->name('callback.tripay');

require __DIR__.'/auth.php';

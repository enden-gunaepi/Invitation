<?php

use App\Http\Controllers\InvitationPublicController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAssetController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TemplateDemoController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::get('/landing/{niche}', [MarketingController::class, 'niche'])->name('marketing.niche');
Route::get('/trial', [MarketingController::class, 'trial'])->name('marketing.trial');
Route::post('/trial/preview', [MarketingController::class, 'trialPreview'])->name('marketing.trial.preview');
Route::get('/r/{referralCode}', [ReferralController::class, 'visit'])->name('referral.visit');

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
});
Route::get('/inv/{slug}/maps', [InvitationPublicController::class, 'mapClick'])->name('invitation.map.click');
Route::middleware(['track.view'])->group(function () {
    Route::get('/inv/{slug}/{token}', [InvitationPublicController::class, 'showGuest'])
        ->where('token', '^(?!maps$).+')
        ->name('invitation.guest');
});
Route::get('/media/music/{invitation}', [PublicAssetController::class, 'music'])
    ->middleware('signed')
    ->name('media.music');
Route::get('/templates/demo/{template:slug}', [TemplateDemoController::class, 'show'])->name('templates.demo');
Route::post('/inv/{slug}/rsvp', [InvitationPublicController::class, 'rsvp'])
    ->middleware('throttle:rsvp-submission')
    ->name('invitation.rsvp');
Route::post('/inv/{slug}/wish', [InvitationPublicController::class, 'wish'])
    ->middleware('throttle:wish-submission')
    ->name('invitation.wish');

// Payment Gateway Callbacks (no auth - called by gateways)
Route::post('/callback/xendit', [PaymentCallbackController::class, 'xenditCallback'])->name('callback.xendit');
Route::post('/callback/tripay', [PaymentCallbackController::class, 'tripayCallback'])->name('callback.tripay');

// Telegram Webhook (no auth - called by Telegram)
Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

require __DIR__.'/auth.php';

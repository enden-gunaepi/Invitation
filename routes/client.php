<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvitationController;
use App\Http\Controllers\Client\GuestController;
use App\Http\Controllers\Client\PhotoController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\AffiliateController;
use App\Http\Controllers\Client\ReminderCampaignController;
use App\Http\Controllers\Client\VendorLeadController;
use Illuminate\Support\Facades\Route;

// Client Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Invitation Management
Route::resource('invitations', InvitationController::class);
Route::patch('/invitations/{invitation}/submit', [InvitationController::class, 'submit'])->name('invitations.submit');
Route::post('/invitations/{invitation}/upgrade-suggested', [InvitationController::class, 'upgradeSuggested'])->name('invitations.upgrade-suggested');
Route::get('/invitations/{invitation}/analytics', [InvitationController::class, 'analytics'])->name('invitations.analytics');

// Guest Management (nested under invitations)
Route::get('/invitations/{invitation}/guests', [GuestController::class, 'index'])->name('invitations.guests.index');
Route::post('/invitations/{invitation}/guests', [GuestController::class, 'store'])->name('invitations.guests.store');
Route::post('/invitations/{invitation}/guests/import', [GuestController::class, 'import'])->name('invitations.guests.import');
Route::post('/invitations/{invitation}/guests/auto-seat', [GuestController::class, 'autoSeatAssign'])->name('invitations.guests.auto-seat');
Route::get('/invitations/{invitation}/checkin', [GuestController::class, 'checkin'])->name('invitations.checkin');
Route::post('/invitations/{invitation}/checkin', [GuestController::class, 'checkinScan'])->name('invitations.checkin.scan');
Route::delete('/invitations/{invitation}/guests/{guest}', [GuestController::class, 'destroy'])->name('invitations.guests.destroy');

// Reminder Campaigns (WhatsApp)
Route::post('/invitations/{invitation}/reminders', [ReminderCampaignController::class, 'store'])->name('invitations.reminders.store');
Route::patch('/invitations/{invitation}/reminders/{campaign}/cancel', [ReminderCampaignController::class, 'cancel'])->name('invitations.reminders.cancel');

// Vendor CRM
Route::post('/invitations/{invitation}/vendors', [VendorLeadController::class, 'store'])->name('invitations.vendors.store');
Route::patch('/invitations/{invitation}/vendors/{vendor}', [VendorLeadController::class, 'update'])->name('invitations.vendors.update');
Route::delete('/invitations/{invitation}/vendors/{vendor}', [VendorLeadController::class, 'destroy'])->name('invitations.vendors.destroy');

// Photo Management (nested under invitations)
Route::post('/invitations/{invitation}/photos', [PhotoController::class, 'store'])->name('invitations.photos.store');
Route::delete('/invitations/{invitation}/photos/{photo}', [PhotoController::class, 'destroy'])->name('invitations.photos.destroy');

// Checkout & Payment
Route::get('/checkout/{invitation}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{invitation}/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/{invitation}/status', [CheckoutController::class, 'status'])->name('checkout.status');
Route::post('/checkout/{invitation}/simulate-paid', [CheckoutController::class, 'simulatePaid'])->name('checkout.simulate-paid');

// Affiliate
Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
Route::post('/affiliate/payout-request', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout-request');

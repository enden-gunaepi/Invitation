<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\GuestOpsController;
use Illuminate\Support\Facades\Route;

// Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// User Management
Route::resource('users', UserController::class);

// Invitation Management
Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
Route::get('/invitations/{invitation}', [InvitationController::class, 'show'])->name('invitations.show');
Route::patch('/invitations/{id}/approve', [InvitationController::class, 'approve'])->name('invitations.approve');
Route::patch('/invitations/{id}/reject', [InvitationController::class, 'reject'])->name('invitations.reject');
Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

// Template Management
Route::resource('templates', TemplateController::class);

// Package Management
Route::resource('packages', PackageController::class);

// Payment Management
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::patch('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.mark-paid');

// Affiliate Management
Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
Route::patch('/affiliate/{commission}/approve', [AffiliateController::class, 'approve'])->name('affiliate.approve');
Route::patch('/affiliate/{commission}/mark-paid', [AffiliateController::class, 'markPaid'])->name('affiliate.mark-paid');
Route::get('/affiliate/payouts', [AffiliateController::class, 'payouts'])->name('affiliate.payouts');
Route::patch('/affiliate/payouts/{payout}/approve', [AffiliateController::class, 'approvePayout'])->name('affiliate.payouts.approve');
Route::patch('/affiliate/payouts/{payout}/reject', [AffiliateController::class, 'rejectPayout'])->name('affiliate.payouts.reject');
Route::patch('/affiliate/payouts/{payout}/mark-paid', [AffiliateController::class, 'markPayoutPaid'])->name('affiliate.payouts.mark-paid');

// Guest Operation (Admin)
Route::get('/invitations/{invitation}/checkin', [GuestOpsController::class, 'checkin'])->name('invitations.checkin');
Route::post('/invitations/{invitation}/checkin', [GuestOpsController::class, 'checkinScan'])->name('invitations.checkin.scan');

// Payment Gateway Config
Route::get('/payment-gateway', [PaymentGatewayController::class, 'index'])->name('payment-gateway.index');
Route::put('/payment-gateway', [PaymentGatewayController::class, 'update'])->name('payment-gateway.update');
Route::post('/payment-gateway/test', [PaymentGatewayController::class, 'testConnection'])->name('payment-gateway.test');

// Settings
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

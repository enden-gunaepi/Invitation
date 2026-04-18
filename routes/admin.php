<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\GuestOpsController;
use App\Http\Controllers\Admin\ReliabilityController;
use Illuminate\Support\Facades\Route;

// Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/seeders/initial', [DashboardController::class, 'runInitialSeeders'])->name('seeders.initial');

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

// Integration
Route::get('/integration', [IntegrationController::class, 'index'])->name('integration.index');
Route::get('/integration/telegram', [IntegrationController::class, 'telegram'])->name('integration.telegram');
Route::put('/integration/telegram', [IntegrationController::class, 'telegramUpdate'])->name('integration.telegram.update');
Route::post('/integration/telegram/test', [IntegrationController::class, 'telegramTestConnection'])->name('integration.telegram.test');
Route::post('/integration/telegram/test-message', [IntegrationController::class, 'telegramTestMessage'])->name('integration.telegram.test-message');
Route::post('/integration/telegram/set-webhook', [IntegrationController::class, 'telegramSetWebhook'])->name('integration.telegram.set-webhook');
Route::delete('/integration/telegram/delete-webhook', [IntegrationController::class, 'telegramDeleteWebhook'])->name('integration.telegram.delete-webhook');
Route::get('/integration/whatsapp', [IntegrationController::class, 'whatsapp'])->name('integration.whatsapp');
Route::put('/integration/whatsapp', [IntegrationController::class, 'whatsappUpdate'])->name('integration.whatsapp.update');
Route::post('/integration/whatsapp/test', [IntegrationController::class, 'whatsappTestConnection'])->name('integration.whatsapp.test');
Route::post('/integration/whatsapp/test-message', [IntegrationController::class, 'whatsappTestMessage'])->name('integration.whatsapp.test-message');
Route::get('/integration/email', [IntegrationController::class, 'email'])->name('integration.email');

// Reliability Monitoring
Route::get('/system/reliability', [ReliabilityController::class, 'index'])->name('system.reliability');

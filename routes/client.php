<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvitationController;
use App\Http\Controllers\Client\GuestController;
use App\Http\Controllers\Client\PhotoController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\AffiliateController;
use App\Http\Controllers\Client\ReminderCampaignController;
use App\Http\Controllers\Client\VendorLeadController;
use App\Http\Controllers\Client\TemplateCatalogController;
use App\Http\Controllers\Client\InvitationCollaboratorController;
use App\Http\Controllers\Client\InvitationBackupController;
use App\Http\Controllers\Client\ClientPackageController;
use App\Http\Controllers\Client\Planner\OnboardingController as PlannerOnboardingController;
use App\Http\Controllers\Client\Planner\DashboardController as PlannerDashboardController;
use App\Http\Controllers\Client\Planner\ChecklistController as PlannerChecklistController;
use App\Http\Controllers\Client\Planner\BudgetController as PlannerBudgetController;
use App\Http\Controllers\Client\Planner\VendorController as PlannerVendorController;
use App\Http\Controllers\Client\Planner\AdvisorController as PlannerAdvisorController;
use Illuminate\Support\Facades\Route;

// Client Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Invitation Management
Route::get('/templates', [TemplateCatalogController::class, 'index'])->name('templates.index');
Route::get('/packages/select', [ClientPackageController::class, 'select'])->name('packages.select');
Route::post('/packages/select', [ClientPackageController::class, 'store'])->name('packages.select.store');
Route::get('/packages/subscriptions/{subscription}/checkout', [ClientPackageController::class, 'checkoutShow'])->name('packages.checkout.show');
Route::post('/packages/subscriptions/{subscription}/checkout', [ClientPackageController::class, 'checkoutProcess'])->name('packages.checkout.process');
Route::get('/packages/subscriptions/{subscription}/status', [ClientPackageController::class, 'checkoutStatus'])->name('packages.checkout.status');
Route::post('/packages/subscriptions/{subscription}/simulate-paid', [ClientPackageController::class, 'checkoutSimulatePaid'])->name('packages.checkout.simulate-paid');

Route::middleware('active.package')->group(function () {
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});
Route::resource('invitations', InvitationController::class)->except(['create', 'store']);
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

// Collaborators
Route::post('/invitations/{invitation}/collaborators', [InvitationCollaboratorController::class, 'store'])->name('invitations.collaborators.store');
Route::delete('/invitations/{invitation}/collaborators/{collaborator}', [InvitationCollaboratorController::class, 'destroy'])->name('invitations.collaborators.destroy');
Route::patch('/collaborators/{collaborator}/accept', [InvitationCollaboratorController::class, 'accept'])->name('collaborators.accept');

// Backups
Route::post('/invitations/{invitation}/backups', [InvitationBackupController::class, 'store'])->name('invitations.backups.store');
Route::post('/invitations/{invitation}/backups/{backup}/restore', [InvitationBackupController::class, 'restore'])->name('invitations.backups.restore');

// Checkout & Payment
Route::get('/checkout/{invitation}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{invitation}/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/{invitation}/status', [CheckoutController::class, 'status'])->name('checkout.status');
Route::post('/checkout/{invitation}/simulate-paid', [CheckoutController::class, 'simulatePaid'])->name('checkout.simulate-paid');

// Affiliate
Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
Route::post('/affiliate/payout-request', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout-request');

// ─── Wedding Planner ───────────────────────────────
Route::prefix('planner')->name('planner.')->group(function () {
    // Onboarding
    Route::get('/onboarding', [PlannerOnboardingController::class, 'showStep1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [PlannerOnboardingController::class, 'processStep1'])->name('onboarding.step1.process');
    Route::get('/onboarding/step2', [PlannerOnboardingController::class, 'showStep2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [PlannerOnboardingController::class, 'processStep2'])->name('onboarding.step2.process');
    Route::get('/onboarding/step3', [PlannerOnboardingController::class, 'showStep3'])->name('onboarding.step3');
    Route::post('/onboarding/step3', [PlannerOnboardingController::class, 'processStep3'])->name('onboarding.step3.process');

    // Dashboard
    Route::get('/', [PlannerDashboardController::class, 'index'])->name('dashboard');

    // Smart Checklist
    Route::get('/checklist', [PlannerChecklistController::class, 'index'])->name('checklist.index');
    Route::post('/checklist', [PlannerChecklistController::class, 'store'])->name('checklist.store');
    Route::patch('/checklist/{checklist}', [PlannerChecklistController::class, 'update'])->name('checklist.update');
    Route::delete('/checklist/{checklist}', [PlannerChecklistController::class, 'destroy'])->name('checklist.destroy');
    Route::post('/checklist/reorder', [PlannerChecklistController::class, 'reorder'])->name('checklist.reorder');

    // Budget Tracker
    Route::get('/budget', [PlannerBudgetController::class, 'index'])->name('budget.index');
    Route::post('/budget/categories', [PlannerBudgetController::class, 'storeCategory'])->name('budget.categories.store');
    Route::delete('/budget/categories/{category}', [PlannerBudgetController::class, 'destroyCategory'])->name('budget.categories.destroy');
    Route::post('/budget/items', [PlannerBudgetController::class, 'storeItem'])->name('budget.items.store');
    Route::patch('/budget/items/{item}', [PlannerBudgetController::class, 'updateItem'])->name('budget.items.update');
    Route::delete('/budget/items/{item}', [PlannerBudgetController::class, 'destroyItem'])->name('budget.items.destroy');

    // Vendor Management
    Route::get('/vendors', [PlannerVendorController::class, 'index'])->name('vendors.index');
    Route::post('/vendors', [PlannerVendorController::class, 'store'])->name('vendors.store');
    Route::patch('/vendors/{vendor}', [PlannerVendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{vendor}', [PlannerVendorController::class, 'destroy'])->name('vendors.destroy');
    Route::patch('/vendors/{vendor}/dp-paid', [PlannerVendorController::class, 'markDpPaid'])->name('vendors.dp-paid');
    Route::patch('/vendors/{vendor}/full-paid', [PlannerVendorController::class, 'markFullPaid'])->name('vendors.full-paid');

    // AI Advisor
    Route::get('/advisor', [PlannerAdvisorController::class, 'index'])->name('advisor.index');
    Route::post('/advisor/ask', [PlannerAdvisorController::class, 'ask'])->name('advisor.ask');
});

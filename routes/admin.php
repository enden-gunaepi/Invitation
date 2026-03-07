<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SettingController;
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

// Settings
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

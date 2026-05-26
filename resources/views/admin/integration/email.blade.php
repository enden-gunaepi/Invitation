@extends('layouts.admin')
@section('title', 'Integrasi Email')
@section('page-title', 'Integrasi')
@section('page-subtitle', 'Konfigurasi pengiriman email')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sidebar Tab --}}
    <div class="lg:w-56 shrink-0">
        <div class="card p-3">
            <div class="flex flex-row lg:flex-col gap-1">
                <a href="{{ route('admin.integration.telegram') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.telegram*') ? 'bg-blue-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.telegram*') ? 'var(--accent)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-telegram text-lg w-5 text-center"></i>
                    <span>Telegram</span>
                </a>
                <a href="{{ route('admin.integration.whatsapp') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.whatsapp*') ? 'bg-green-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.whatsapp*') ? 'var(--success)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-whatsapp text-lg w-5 text-center"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="{{ route('admin.integration.email') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.email*') ? 'bg-purple-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.email*') ? '#8b5cf6' : 'var(--text-secondary)' }};">
                    <i class="fas fa-envelope text-lg w-5 text-center"></i>
                    <span>Email</span>
                </a>
                <a href="{{ route('admin.integration.payment-gateway') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.payment-gateway*') ? 'bg-amber-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.payment-gateway*') ? '#d97706' : 'var(--text-secondary)' }};">
                    <i class="fas fa-credit-card text-lg w-5 text-center"></i>
                    <span>Payment</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1">
        <div class="card p-12 text-center">
            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                <i class="fas fa-envelope text-3xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">Email Integration</h3>
            <p class="text-sm" style="color: var(--text-secondary);">Fitur ini sedang dalam pengembangan.</p>
        </div>
    </div>
</div>
@endsection

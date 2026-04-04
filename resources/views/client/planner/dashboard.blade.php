@extends('layouts.client')

@section('title', 'Wedding Planner')
@section('page-title', '💍 Wedding Planner')
@section('page-subtitle', $profile->partner_1_name . ' & ' . $profile->partner_2_name)

@section('content')
<div class="space-y-6">
    {{-- Top Row: Countdown + Health Score --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Countdown --}}
        <div class="card p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-gradient-to-br from-rose-500/10 to-pink-500/10 -mr-6 -mt-6"></div>
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);"><i class="fas fa-calendar-heart mr-1"></i> Countdown</p>
            @if($profile->days_remaining !== null)
                <p class="text-3xl font-black bg-gradient-to-r from-rose-600 to-pink-600 bg-clip-text text-transparent">
                    {{ $profile->days_remaining }}
                </p>
                <p class="text-xs font-medium" style="color: var(--text-secondary);">hari menuju hari H</p>
                <p class="text-xs mt-1" style="color: var(--text-tertiary);">{{ $profile->wedding_date->translatedFormat('d F Y') }}</p>
            @else
                <p class="text-sm" style="color: var(--text-secondary);">Tanggal belum diatur</p>
            @endif
        </div>

        {{-- Health Score --}}
        <div class="card p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 rounded-full -mr-6 -mt-6" style="background: {{ $healthScore['color'] }}10;"></div>
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);"><i class="fas fa-heart-pulse mr-1"></i> Health Score</p>
            <p class="text-3xl font-black" style="color: {{ $healthScore['color'] }};">{{ $healthScore['overall'] }}</p>
            <p class="text-xs font-medium">{{ $healthScore['label'] }}</p>
            <a href="{{ route('client.planner.advisor.index') }}" class="text-xs font-semibold mt-1 inline-block" style="color: var(--accent);">Lihat detail →</a>
        </div>

        {{-- Budget --}}
        <div class="card p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-gradient-to-br from-amber-500/10 to-yellow-500/10 -mr-6 -mt-6"></div>
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);"><i class="fas fa-wallet mr-1"></i> Budget</p>
            <p class="text-lg font-bold mb-1">Rp{{ number_format($budgetSummary['used'], 0, ',', '.') }}</p>
            <div class="w-full h-2 rounded-full bg-gray-100 mb-1">
                <div class="h-full rounded-full transition-all duration-500 {{ $budgetSummary['percent'] > 100 ? 'bg-red-500' : ($budgetSummary['percent'] > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                    style="width: {{ min(100, $budgetSummary['percent']) }}%"></div>
            </div>
            <p class="text-xs" style="color: var(--text-secondary);">{{ $budgetSummary['percent'] }}% dari Rp{{ number_format($budgetSummary['total'], 0, ',', '.') }}</p>
        </div>

        {{-- Checklist --}}
        <div class="card p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-gradient-to-br from-emerald-500/10 to-teal-500/10 -mr-6 -mt-6"></div>
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);"><i class="fas fa-list-check mr-1"></i> Checklist</p>
            <p class="text-3xl font-black text-emerald-600">{{ $checklistProgress }}%</p>
            <p class="text-xs" style="color: var(--text-secondary);">{{ $checklistDone }}/{{ $checklistTotal }} selesai</p>
            @if($overdueTasks > 0)
                <p class="text-xs font-semibold text-red-500 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $overdueTasks }} terlambat</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Urgent Tasks + Quick Actions --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Urgent Tasks --}}
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: var(--border);">
                    <h3 class="font-bold text-sm"><i class="fas fa-fire text-red-500 mr-2"></i>Task Mendesak</h3>
                    <a href="{{ route('client.planner.checklist.index') }}" class="text-xs font-semibold" style="color: var(--accent);">Lihat semua →</a>
                </div>
                <div class="p-4">
                    @forelse($urgentTasks as $task)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-black/[0.02] transition mb-1">
                        <form method="POST" action="{{ route('client.planner.checklist.update', $task) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="done">
                            <button type="submit" class="w-5 h-5 rounded-md border-2 flex items-center justify-center shrink-0 transition hover:border-emerald-400 hover:bg-emerald-50
                                {{ $task->isOverdue() ? 'border-red-400' : 'border-amber-400' }}">
                            </button>
                        </form>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $task->title }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">
                                @if($task->deadline)
                                    @if($task->isOverdue())
                                        <span class="text-red-500 font-semibold">Terlambat {{ abs(now()->diffInDays($task->deadline)) }} hari!</span>
                                    @else
                                        <span class="text-amber-500">{{ $task->deadline->diffForHumans() }}</span>
                                    @endif
                                @endif
                                · {{ ucfirst($task->category) }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8" style="color: var(--text-secondary);">
                        <i class="fas fa-check-circle text-3xl text-emerald-400 mb-3"></i>
                        <p class="text-sm">Tidak ada task mendesak! 🎉</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('client.planner.checklist.index') }}" class="card p-4 text-center hover:shadow-lg transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-list-check text-emerald-600"></i>
                    </div>
                    <p class="text-xs font-bold">Checklist</p>
                </a>
                <a href="{{ route('client.planner.budget.index') }}" class="card p-4 text-center hover:shadow-lg transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-wallet text-amber-600"></i>
                    </div>
                    <p class="text-xs font-bold">Budget</p>
                </a>
                <a href="{{ route('client.planner.vendors.index') }}" class="card p-4 text-center hover:shadow-lg transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-store text-violet-600"></i>
                    </div>
                    <p class="text-xs font-bold">Vendor</p>
                </a>
                <a href="{{ route('client.planner.advisor.index') }}" class="card p-4 text-center hover:shadow-lg transition-all duration-200 group">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <i class="fas fa-robot text-rose-600"></i>
                    </div>
                    <p class="text-xs font-bold">AI Advisor</p>
                </a>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-6">
            {{-- Vendor Summary --}}
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-3"><i class="fas fa-store text-violet-500 mr-2"></i>Vendor</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Total Vendor</span>
                        <span class="font-bold">{{ $vendorTotal }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Sudah Deal</span>
                        <span class="font-bold text-emerald-600">{{ $vendorSecured }}</span>
                    </div>
                    @if($vendorPaymentDue > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-amber-500"><i class="fas fa-exclamation-triangle mr-1"></i>Payment due</span>
                        <span class="font-bold text-amber-500">{{ $vendorPaymentDue }}</span>
                    </div>
                    @endif
                </div>
                <a href="{{ route('client.planner.vendors.index') }}" class="block text-center text-xs font-semibold mt-3 py-2 rounded-lg transition hover:bg-violet-50" style="color: var(--accent);">
                    Kelola Vendor →
                </a>
            </div>

            {{-- RSVP Integration --}}
            @if($rsvpData)
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-3"><i class="fas fa-users text-sky-500 mr-2"></i>RSVP (dari Undangan)</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Total RSVP</span>
                        <span class="font-bold">{{ $rsvpData['total'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Konfirmasi Hadir</span>
                        <span class="font-bold text-emerald-600">{{ $rsvpData['attending'] }} pax</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span style="color: var(--text-secondary);">Estimasi Porsi</span>
                        <span class="font-bold text-amber-600">{{ $rsvpData['estimasi_porsi'] }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Health Score Breakdown --}}
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-3"><i class="fas fa-heart-pulse mr-2" style="color: {{ $healthScore['color'] }};"></i>Health Breakdown</h3>
                <div class="space-y-3">
                    @foreach($healthScore['dimensions'] as $key => $dim)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-semibold capitalize">{{ $key }}</span>
                            <span class="font-bold">{{ $dim['score'] }}%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100">
                            <div class="h-full rounded-full transition-all duration-500
                                {{ $dim['score'] >= 70 ? 'bg-emerald-500' : ($dim['score'] >= 40 ? 'bg-amber-500' : 'bg-red-500') }}"
                                style="width: {{ $dim['score'] }}%"></div>
                        </div>
                        <p class="text-xs mt-0.5" style="color: var(--text-tertiary);">{{ $dim['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

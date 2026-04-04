@extends('layouts.client')

@section('title', 'AI Wedding Advisor')
@section('page-title', '🧠 AI Wedding Advisor')
@section('page-subtitle', 'Asisten pernikahan cerdas')

@section('content')
<div class="space-y-6">
    {{-- Health Score Hero --}}
    <div class="card p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 rounded-full -mr-10 -mt-10" style="background: {{ $healthScore['color'] }}10;"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--text-secondary);">Wedding Health Score</p>
                <div class="flex items-end gap-3 mb-2">
                    <p class="text-6xl font-black" style="color: {{ $healthScore['color'] }};">{{ $healthScore['overall'] }}</p>
                    <p class="text-lg font-bold mb-2" style="color: {{ $healthScore['color'] }};">/100</p>
                </div>
                <p class="text-lg font-bold">{{ $healthScore['label'] }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Skor dihitung dari 4 dimensi: Checklist, Budget, Vendor, dan Timeline</p>
            </div>
            <div class="space-y-3">
                @foreach($healthScore['dimensions'] as $key => $dim)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold capitalize">
                            @switch($key)
                                @case('checklist') <i class="fas fa-list-check text-emerald-500 mr-1"></i> Checklist @break
                                @case('budget') <i class="fas fa-wallet text-amber-500 mr-1"></i> Budget @break
                                @case('vendor') <i class="fas fa-store text-violet-500 mr-1"></i> Vendor @break
                                @case('timeline') <i class="fas fa-calendar text-sky-500 mr-1"></i> Timeline @break
                            @endswitch
                        </span>
                        <span class="font-black {{ $dim['score'] >= 70 ? 'text-emerald-600' : ($dim['score'] >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $dim['score'] }}%
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-gray-100">
                        <div class="h-full rounded-full transition-all duration-700
                            {{ $dim['score'] >= 70 ? 'bg-emerald-500' : ($dim['score'] >= 40 ? 'bg-amber-500' : 'bg-red-500') }}"
                            style="width: {{ $dim['score'] }}%"></div>
                    </div>
                    <p class="text-xs mt-0.5" style="color: var(--text-tertiary);">{{ $dim['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Chat --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-4"><i class="fas fa-robot text-rose-500 mr-2"></i>Tanya AI Advisor</h3>

                <form method="POST" action="{{ route('client.planner.advisor.ask') }}" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="question" class="form-input flex-1 text-sm" placeholder="Tanya apa saja... contoh: Budget 50jt cukup gak?" required>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold text-white text-sm
                        bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 transition shrink-0">
                        <i class="fas fa-paper-plane mr-1"></i> Tanya
                    </button>
                </form>

                {{-- Latest Answer --}}
                @if(session('advisor_answer'))
                <div class="p-4 rounded-2xl border mb-4" style="background: var(--bg-tertiary); border-color: var(--border);">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center shrink-0">
                            <i class="fas fa-robot text-white text-xs"></i>
                        </div>
                        <div class="flex-1 text-sm prose prose-sm max-w-none" style="color: var(--text);">
                            {!! nl2br(e(session('advisor_answer'))) !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- Suggestions --}}
                <div>
                    <p class="text-xs font-bold mb-2" style="color: var(--text-secondary);">💡 Coba tanya:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            'Budget saya cukup gak?',
                            'Vendor apa yang harus di-booking duluan?',
                            'Hari ini harus ngapain?',
                            'Estimasi konsumsi berapa porsi?',
                            'Status persiapan saya gimana?',
                        ] as $suggestion)
                        <form method="POST" action="{{ route('client.planner.advisor.ask') }}" class="inline">
                            @csrf
                            <input type="hidden" name="question" value="{{ $suggestion }}">
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium border hover:bg-gray-50 transition" style="border-color: var(--border);">
                                {{ $suggestion }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- History --}}
            @if($recentLogs->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="px-5 py-3 border-b" style="border-color: var(--border);">
                    <h3 class="font-bold text-xs" style="color: var(--text-secondary);">Riwayat Konsultasi</h3>
                </div>
                <div class="divide-y" style="border-color: var(--border);">
                    @foreach($recentLogs as $log)
                    <div class="p-4" x-data="{ open: false }">
                        <div class="flex items-start gap-3 cursor-pointer" @click="open = !open">
                            <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-user text-xs" style="color: var(--text-tertiary);"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold">{{ $log->question }}</p>
                                <p class="text-xs" style="color: var(--text-tertiary);">{{ $log->created_at->diffForHumans() }} · {{ ucfirst($log->category) }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''" style="color: var(--text-tertiary);"></i>
                        </div>
                        <div x-show="open" x-transition class="mt-3 ml-10 p-3 rounded-xl text-sm" style="background: var(--bg-tertiary);">
                            {!! nl2br(e($log->answer)) !!}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Quick Tips --}}
        <div class="space-y-4">
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-3"><i class="fas fa-lightbulb text-amber-500 mr-2"></i>Tips Cepat</h3>
                <div class="space-y-3">
                    @php
                        $tips = [
                            ['icon' => 'fa-envelope', 'color' => 'text-rose-500', 'text' => 'Gunakan undangan digital untuk hemat biaya cetak Rp3-5 juta!'],
                            ['icon' => 'fa-calendar-check', 'color' => 'text-blue-500', 'text' => 'Booking venue minimal H-10 bulan, venue populer cepat penuh'],
                            ['icon' => 'fa-utensils', 'color' => 'text-amber-500', 'text' => 'Siapkan porsi makanan +10% dari estimasi tamu hadir'],
                            ['icon' => 'fa-camera', 'color' => 'text-emerald-500', 'text' => 'Fotografer baik biasanya sudah full 6 bulan sebelumnya'],
                            ['icon' => 'fa-umbrella', 'color' => 'text-sky-500', 'text' => 'Selalu siapkan Plan B untuk cuaca jika outdoor'],
                        ];
                    @endphp
                    @foreach($tips as $tip)
                    <div class="flex gap-2">
                        <i class="fas {{ $tip['icon'] }} {{ $tip['color'] }} mt-0.5 text-xs"></i>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $tip['text'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-5">
                <a href="{{ route('client.planner.dashboard') }}" class="flex items-center gap-2 text-sm font-semibold" style="color: var(--accent);">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

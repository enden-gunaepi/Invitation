@extends('layouts.client')

@section('title', 'Vendor Management')
@section('page-title', '🧾 Vendor Management')
@section('page-subtitle', $stats['total'] . ' vendor — ' . $stats['deal'] + $stats['dp_paid'] + $stats['lunas'] . ' sudah deal')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'text-gray-700'],
            ['label' => 'Prospek', 'value' => $stats['prospek'], 'color' => 'text-slate-500'],
            ['label' => 'Deal', 'value' => $stats['deal'], 'color' => 'text-blue-600'],
            ['label' => 'DP Lunas', 'value' => $stats['dp_paid'], 'color' => 'text-amber-600'],
            ['label' => 'Lunas', 'value' => $stats['lunas'], 'color' => 'text-emerald-600'],
            ['label' => 'Cancelled', 'value' => $stats['cancelled'], 'color' => 'text-red-500'],
        ] as $stat)
        <div class="card p-3 text-center">
            <p class="text-xl font-black {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs" style="color: var(--text-secondary);">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('client.planner.vendors.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
            {{ !request('status') && !request('category') ? 'bg-rose-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">Semua</a>
        @foreach($statusOptions as $key => $label)
        <a href="{{ route('client.planner.vendors.index', ['status' => $key]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
            {{ request('status') === $key ? 'bg-rose-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">{{ $label }}</a>
        @endforeach
        <select onchange="if(this.value) window.location.href=this.value" class="form-input py-1.5 px-2 text-xs rounded-lg ml-auto" style="width: auto;">
            <option value="{{ route('client.planner.vendors.index', ['status' => request('status')]) }}">Semua Kategori</option>
            @foreach($categoryOptions as $key => $label)
            <option value="{{ route('client.planner.vendors.index', ['status' => request('status'), 'category' => $key]) }}"
                {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Vendor List --}}
        <div class="lg:col-span-2 space-y-3">
            @forelse($vendors as $vendor)
            <div class="card p-5" x-data="{ expanded: false }">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white text-sm shrink-0">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-bold">{{ $vendor->name }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">{{ $categoryOptions[$vendor->category] ?? ucfirst($vendor->category) }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-lg text-xs font-bold shrink-0
                                {{ match($vendor->status) {
                                    'lunas' => 'bg-emerald-100 text-emerald-700',
                                    'dp_paid' => 'bg-amber-100 text-amber-700',
                                    'deal' => 'bg-blue-100 text-blue-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ $statusOptions[$vendor->status] ?? $vendor->status }}
                            </span>
                        </div>

                        @if($vendor->price > 0)
                        <p class="text-sm font-bold mt-2">Rp{{ number_format($vendor->price, 0, ',', '.') }}</p>
                        @endif

                        {{-- Contact Links --}}
                        <div class="flex items-center gap-3 mt-2">
                            @if($vendor->whatsapp_url)
                            <a href="{{ $vendor->whatsapp_url }}" target="_blank" class="text-xs text-emerald-600 hover:underline"><i class="fab fa-whatsapp mr-1"></i>WA</a>
                            @endif
                            @if($vendor->instagram_url)
                            <a href="{{ $vendor->instagram_url }}" target="_blank" class="text-xs text-pink-600 hover:underline"><i class="fab fa-instagram mr-1"></i>IG</a>
                            @endif
                            @if($vendor->phone)
                            <span class="text-xs" style="color: var(--text-secondary);"><i class="fas fa-phone mr-1"></i>{{ $vendor->phone }}</span>
                            @endif
                        </div>

                        @if($vendor->isPaymentDueSoon())
                        <p class="text-xs font-semibold text-amber-500 mt-2"><i class="fas fa-exclamation-triangle mr-1"></i>Deadline bayar {{ $vendor->payment_deadline->diffForHumans() }}</p>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($vendor->status === 'prospek' || $vendor->status === 'deal')
                            <form method="POST" action="{{ route('client.planner.vendors.update', $vendor) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $vendor->status === 'prospek' ? 'deal' : 'dp_paid' }}">
                                <button type="submit" class="px-3 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    {{ $vendor->status === 'prospek' ? '✓ Deal' : '💰 DP Paid' }}
                                </button>
                            </form>
                            @endif
                            @if(in_array($vendor->status, ['deal', 'dp_paid']))
                            <form method="POST" action="{{ route('client.planner.vendors.full-paid', $vendor) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition">
                                    ✓ Lunas
                                </button>
                            </form>
                            @endif
                            <button @click="expanded = !expanded" class="px-3 py-1 rounded-lg text-xs font-semibold bg-gray-50 hover:bg-gray-100 transition" style="color: var(--text-secondary);">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Expanded Details --}}
                <div x-show="expanded" x-transition class="mt-4 pt-4 border-t" style="border-color: var(--border);">
                    @if($vendor->notes)<p class="text-xs mb-2" style="color: var(--text-secondary);">📝 {{ $vendor->notes }}</p>@endif
                    @if($vendor->contact_person)<p class="text-xs" style="color: var(--text-secondary);">👤 {{ $vendor->contact_person }}</p>@endif
                    @if($vendor->dp_amount > 0 && $vendor->dp_paid_at)
                    <p class="text-xs text-emerald-600 mt-1">✅ DP Rp{{ number_format($vendor->dp_amount, 0, ',', '.') }} ({{ $vendor->dp_paid_at->format('d M Y') }})</p>
                    @endif
                    <form method="POST" action="{{ route('client.planner.vendors.destroy', $vendor) }}" onsubmit="return confirm('Hapus vendor ini?')" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600"><i class="fas fa-trash mr-1"></i>Hapus vendor</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="card p-8 text-center" style="color: var(--text-secondary);">
                <i class="fas fa-store text-4xl mb-3 opacity-30"></i>
                <p class="text-sm">Belum ada vendor. Tambahkan vendor pertama!</p>
            </div>
            @endforelse
        </div>

        {{-- Right: Add Vendor --}}
        <div>
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-4"><i class="fas fa-plus-circle text-violet-500 mr-2"></i>Tambah Vendor</h3>
                <form method="POST" action="{{ route('client.planner.vendors.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold mb-1">Kategori *</label>
                        <select name="category" class="form-input w-full text-sm" required>
                            @foreach($categoryOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Nama Vendor *</label>
                        <input type="text" name="name" class="form-input w-full text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Contact Person</label>
                        <input type="text" name="contact_person" class="form-input w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">No. HP / WhatsApp</label>
                        <input type="text" name="phone" class="form-input w-full text-sm" placeholder="08xx">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Instagram</label>
                        <input type="text" name="instagram" class="form-input w-full text-sm" placeholder="@vendor">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Harga (Rp)</label>
                        <input type="number" name="price" class="form-input w-full text-sm" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">DP (Rp)</label>
                        <input type="number" name="dp_amount" class="form-input w-full text-sm" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Deadline Bayar</label>
                        <input type="date" name="payment_deadline" class="form-input w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Catatan</label>
                        <textarea name="notes" class="form-input w-full text-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white text-sm
                        bg-gradient-to-r from-violet-500 to-purple-500 hover:from-violet-600 hover:to-purple-600 transition shadow-lg shadow-violet-500/20">
                        <i class="fas fa-plus mr-1"></i> Tambah Vendor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

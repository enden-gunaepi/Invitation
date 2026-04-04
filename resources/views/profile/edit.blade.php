@extends(auth()->check() && auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.client')
@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')
@section('page-subtitle', 'Kelola akun dan keamanan')

@section('content')
@php
/** @var \App\Models\User $user */
@endphp
<style>
    .profile-shell {
        max-width: 980px;
    }
    .mac-panel {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, .22);
        background: linear-gradient(180deg, rgba(255,255,255,.88), rgba(248,250,252,.78));
        box-shadow: 0 14px 34px rgba(15, 23, 42, .10);
        overflow: hidden;
        backdrop-filter: blur(8px);
    }
    .mac-head {
        padding: .75rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, .20);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255,255,255,.6);
    }
    .mac-dots { display: flex; gap: .35rem; }
    .mac-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,.15);
    }
    .mac-dot.red { background: #ff5f57; }
    .mac-dot.yellow { background: #febc2e; }
    .mac-dot.green { background: #28c840; }
    .mac-title {
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-secondary);
        letter-spacing: .02em;
    }
    .mac-body {
        padding: 1.2rem;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .profile-block {
        border: 1px solid rgba(148, 163, 184, .22);
        background: rgba(255,255,255,.85);
        border-radius: 14px;
        padding: 1rem;
    }
    .profile-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .11em;
        color: var(--text-secondary);
        margin-bottom: .25rem;
    }
    .profile-title {
        font-weight: 700;
        color: var(--accent);
        margin-bottom: .15rem;
    }
    .profile-sub {
        font-size: .8rem;
        color: var(--text-secondary);
        margin-bottom: .8rem;
    }
    @media (min-width: 900px) {
        .mac-body { grid-template-columns: 1fr 1fr; }
        .profile-block.full { grid-column: 1 / -1; }
    }
</style>

<div class="profile-shell space-y-6" x-data="{ tab: 'profile' }">
    {{-- Tabs Navigation --}}
    <div class="flex gap-2 p-1 bg-gray-100/50 rounded-xl max-w-sm backdrop-blur-sm border border-gray-200/50">
        <button @click="tab = 'profile'"
            class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold transition-all"
            :class="tab === 'profile' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
            <i class="fas fa-user mr-2"></i>Profile Pribadi
        </button>
        <button @click="tab = 'company'"
             class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold transition-all"
            :class="tab === 'company' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
            <i class="fas fa-building mr-2"></i>Perusahaan
        </button>
    </div>

    <div class="mac-panel max-w-2xl">
        <div class="mac-head">
            <div class="mac-dots">
                <span class="mac-dot red"></span>
                <span class="mac-dot yellow"></span>
                <span class="mac-dot green"></span>
            </div>
            <div class="mac-title" x-text="tab === 'profile' ? '192.168.1.1 — Profile Settings' : '192.168.1.1 — Company Settings'"></div>
            <div style="width:52px;"></div>
        </div>

        {{-- TAB: PROFILE PRIBADI --}}
        <div class="mac-body !grid-cols-1" x-show="tab === 'profile'" x-transition>
            <div class="profile-block">
                <div class="profile-label">Section</div>
                <h3 class="profile-title"><i class="fas fa-user mr-2"></i>Informasi Akun Pribadi</h3>
                <p class="profile-sub">Update foto profil, nama, dan email akun Anda.</p>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}"></form>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden shadow-sm shrink-0 border border-gray-300">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xl font-bold bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-800">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="form-label mb-1">Foto Profil Pribadi</label>
                            <input type="file" name="avatar" class="form-input text-xs w-full pb-2 pt-2 h-auto" accept="image/*">
                            @error('avatar') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                        @error('name') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                        @error('email') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <p class="text-xs mt-2" style="color: var(--warning);">
                                Email belum terverifikasi.
                                <button form="send-verification" class="underline ml-1" style="color: var(--accent);">Kirim ulang verifikasi</button>
                            </p>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
                </form>
            </div>

            <div class="profile-block">
                <div class="profile-label">Section</div>
                <h3 class="profile-title"><i class="fas fa-lock mr-2"></i>Keamanan</h3>
                <p class="profile-sub">Ganti password akun Anda.</p>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" class="form-input">
                        @error('current_password', 'updatePassword') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-input">
                        @error('password', 'updatePassword') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-input">
                        @error('password_confirmation', 'updatePassword') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-key mr-2"></i>Update Password</button>
                </form>
            </div>
        </div>

        {{-- TAB: PERUSAHAAN --}}
        <div class="mac-body !grid-cols-1" x-show="tab === 'company'" style="display: none;" x-transition>
            <div class="profile-block">
                <div class="profile-label">Section</div>
                <h3 class="profile-title"><i class="fas fa-building mr-2"></i>Profil Perusahaan</h3>
                <p class="profile-sub">Data ini akan digunakan sebagai brand di dashboard bagian kiri atas.</p>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('patch')

                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-xl bg-gray-200 overflow-hidden shadow-sm shrink-0 border border-gray-300">
                            @if($user->company_logo)
                                <img src="{{ Storage::url($user->company_logo) }}" alt="Logo Perusahaan" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-2xl font-bold bg-gradient-to-br from-gray-700 to-black text-white">
                                    <i class="fas fa-building"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="form-label mb-1">Logo Perusahaan</label>
                            <input type="file" name="company_logo" class="form-input text-xs w-full pb-2 pt-2 h-auto" accept="image/*">
                            @error('company_logo') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Nama Perusahaan / Brand</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" class="form-input" placeholder="Contoh: Digital Invitation Vendor">
                        @error('company_name') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i>Simpan Profil Perusahaan</button>
                </form>
            </div>
            
            <div class="profile-block">
                <div class="profile-label">Info</div>
                <h3 class="profile-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Program Affiliate</h3>
                <p class="profile-sub">Ajak kreator lain menggunakan platform ini dan dapatkan komisi.</p>
                <div>
                    <label class="form-label">Kode Referral Anda</label>
                    <input type="text" class="form-input" value="{{ $user->referral_code ?? '-' }}" readonly>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">
                        Link referral: <code>{{ route('referral.visit', ['referralCode' => $user->referral_code]) }}</code>
                    </p>
                </div>
            </div>
        </div>
    </div>

            <div class="profile-block full" style="border-color: rgba(255,59,48,.35);">
                <div class="profile-label">Section</div>
                <h3 class="profile-title" style="color: var(--danger);"><i class="fas fa-triangle-exclamation mr-2"></i>Zona Berbahaya</h3>
                <p class="profile-sub">Logout atau hapus akun dari perangkat ini.</p>

                <form method="post" action="{{ route('logout') }}" class="mb-4">
                    @csrf
                    <button type="submit" class="btn text-sm" style="background: rgba(0,113,227,.1); color: var(--info); border: 1px solid rgba(0,113,227,.25);">
                        <i class="fas fa-arrow-right-from-bracket mr-2"></i>Logout
                    </button>
                </form>

                <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini permanen.')">
                    @csrf
                    @method('delete')
                    <div class="mb-3">
                        <label class="form-label">Password Konfirmasi</label>
                        <input type="password" name="password" class="form-input" placeholder="Masukkan password">
                        @error('password', 'userDeletion') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn text-sm" style="background: rgba(255,59,48,.12); color: var(--danger); border: 1px solid rgba(255,59,48,.25);">
                        <i class="fas fa-trash mr-2"></i>Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

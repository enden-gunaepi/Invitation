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
        width: 100%;
        max-width: 88rem;
        margin-inline: auto;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .profile-sections {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .profile-block {
        border: 1px solid rgba(148, 163, 184, .22);
        background: rgba(255,255,255,.88);
        border-radius: 14px;
        padding: 1.15rem;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
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
        .profile-sections {
            grid-template-columns: 1fr 1fr;
        }

        .profile-block.full {
            grid-column: 1 / -1;
        }
    }

    @media (min-width: 1200px) {
        .profile-layout {
            grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.55fr);
            align-items: start;
        }
    }
</style>

<div class="profile-shell space-y-6">
    <div class="profile-layout">
        <div class="profile-sections">
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
                                <div class="w-full h-full flex items-center justify-center text-xl font-bold bg-gradient-to-br from-pink-100 to-rose-100 text-pink-800">
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

            <div class="profile-block full">
                @if(auth()->user()->role === 'admin')
                    <div class="mb-5 rounded-2xl border border-pink-200 bg-pink-50/80 px-4 py-4 text-sm text-pink-700">
                        Nama brand dan logo aplikasi sekarang dikelola terpusat dari menu <strong>Pengaturan Admin → Data Perusahaan</strong> agar tampil konsisten di landing page, auth, dan seluruh sidebar.
                    </div>
                @endif

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

        <div class="hidden xl:flex flex-col items-center justify-center p-8 bg-white border border-gray-200/50 rounded-2xl shadow-sm" style="background: linear-gradient(180deg, rgba(255,255,255,.88), rgba(248,250,252,.78)); backdrop-filter: blur(8px); min-height: 100%;">
            <img src="{{ asset('assets/maskot/akunsaya.png') }}" alt="Akun Saya Mascot" class="h-40 w-auto mb-4 drop-shadow-md" style="animation: float 4s ease-in-out infinite;">
            <h4 class="text-sm font-semibold text-primary mb-1">Pengaturan Akun</h4>
            <p class="text-xs text-center text-on-surface-variant/80 px-4 leading-relaxed">Kelola preferensi akun Anda, detail personal, kredensial keamanan, dan program affiliate secara terpusat.</p>
        </div>
    </div>
</div>
@endsection

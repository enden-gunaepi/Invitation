# 💌 InvitePro — Sistem Undangan Digital

Platform undangan digital berbasis web yang memungkinkan pembuatan, pengelolaan, dan distribusi undangan online untuk berbagai acara seperti pernikahan, ulang tahun, dan lainnya.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Tech Stack](#-tech-stack)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [Role & Hak Akses](#-role--hak-akses)
- [Template Undangan](#-template-undangan)
- [Struktur Database](#-struktur-database)
- [Struktur Folder](#-struktur-folder)
- [Screenshot](#-screenshot)
- [Lisensi](#-lisensi)

---

## ✨ Fitur

### 👑 Administrator
- Dashboard statistik (total undangan, tamu, RSVP, template aktif)
- Manajemen User (Client & Guest) — CRUD
- Manajemen Template Undangan — CRUD
- Manajemen Paket / Pricing — CRUD
- Approve / Reject undangan dari client
- Pengaturan sistem (nama app, email, logo, dll)

### 🧑‍💼 Client (Pemilik Acara)
- Register & Login
- Pilih template undangan
- Input data acara (nama mempelai, tanggal, lokasi, dll)
- Upload foto / galeri
- Kelola daftar tamu
- Kirim link undangan personal per tamu
- Pantau RSVP & ucapan
- Lihat statistik kunjungan undangan

### 👤 Guest (Tamu)
- Akses undangan via link unik
- Lihat detail acara dengan animasi
- Countdown timer menuju hari H
- RSVP (konfirmasi kehadiran)
- Kirim ucapan & doa
- Simpan ke Google Calendar
- Lihat lokasi di Google Maps

### 🎨 Desain
- **Dark / Light Mode** — toggle dengan persist di localStorage
- **Responsive** — mobile & desktop friendly
- **macOS-inspired UI** — translucent blur sidebar, rounded corners, Inter font
- **Multi-template** — 4 template bawaan dengan desain unik
- **Custom color scheme** — setiap undangan bisa punya warna sendiri

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 3.x, Alpine.js |
| Database | MySQL 8.0 |
| Auth | Laravel Breeze |
| Animation | AOS (Animate On Scroll) |
| Icons | Font Awesome 6.5 |
| Build Tool | Vite |

---

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- MySQL 8.0

### Langkah-langkah

```bash
# 1. Clone repository
git clone git@github.com:enden-gunaepi/Invitation.git
cd Invitation

# 2. Install PHP dependencies
composer install

# 3. Install NPM packages
npm install

# 4. Copy file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env (lihat bagian Konfigurasi)

# 7. Jalankan migrasi & seeder
php artisan migrate --seed

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Build assets
npm run build

# 10. Jalankan server
php artisan serve
```

Buka browser → `http://127.0.0.1:8000`

---

## ⚙ Konfigurasi

Edit file `.env` dan sesuaikan:

```env
APP_NAME="InvitePro"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invitation_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📖 Penggunaan

### Setup Awal

1. Buka `/register` dan buat akun pertama — **otomatis menjadi Administrator**
2. Semua registrasi berikutnya menjadi **Client**
3. Admin bisa masuk ke `/admin` untuk mengelola sistem

### Alur Penggunaan

```
Register (Admin) → Setup Template & Paket
                          ↓
        Client Register → Pilih Template → Buat Undangan
                          ↓
        Admin Approve → Undangan Aktif → Share Link ke Tamu
                          ↓
        Tamu Akses → RSVP & Kirim Ucapan
```

---

## 🔐 Role & Hak Akses

| Role | Prefix Route | Akses |
|------|-------------|-------|
| **Admin** | `/admin/*` | Full akses ke semua fitur manajemen |
| **Client** | `/client/*` | Buat & kelola undangan sendiri |
| **Guest** | `/inv/{slug}` | View undangan, RSVP, kirim ucapan |

Middleware `RoleMiddleware` di `app/Http/Middleware/RoleMiddleware.php` mengatur akses berdasarkan role.

---

## 🎨 Template Undangan

Sistem mendukung multi-template. Setiap template adalah folder Blade terpisah:

```
resources/views/invitations/templates/
├── wedding-elegant/index.blade.php     → Gold/dark, partikel emas, shimmer text
├── wedding-rustic/index.blade.php      → Earthy brown, dekorasi daun, serif font
├── birthday-fun/index.blade.php        → Colorful confetti, emoji, Pacifico font
└── wedding-minimalist/index.blade.php  → Light mode B&W, Bodoni Moda, ultra-clean
```

### Menambahkan Template Baru

1. Buat folder: `resources/views/invitations/templates/{nama-template}/index.blade.php`
2. Desain HTML/CSS/JS (gunakan variabel `$invitation` dan `$guest`)
3. Tambahkan record di database:
   ```php
   Template::create([
       'name' => 'Nama Template',
       'slug' => 'nama-template',
       'category' => 'wedding',
       'html_path' => 'invitations.templates.nama-template.index',
       'is_active' => true,
   ]);
   ```
4. Template siap dipilih oleh client!

---

## 🗄 Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Users dengan role (admin, client, guest) |
| `packages` | Paket harga (Basic, Premium, Exclusive) |
| `templates` | Template undangan dengan `html_path` |
| `invitations` | Data undangan utama |
| `invitation_photos` | Foto/galeri undangan |
| `invitation_events` | Sub-event (akad, resepsi, dll) |
| `guests` | Daftar tamu dengan link unik |
| `rsvps` | Konfirmasi kehadiran |
| `wishes` | Ucapan & doa dari tamu |
| `invitation_views` | Tracking kunjungan |
| `payments` | Data pembayaran |
| `settings` | Pengaturan aplikasi |

---

## 📁 Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          → DashboardController, UserController, dll
│   │   ├── Client/         → DashboardController, InvitationController, dll
│   │   └── Auth/           → RegisteredUserController (first = admin)
│   └── Middleware/
│       ├── RoleMiddleware.php
│       └── TrackInvitationView.php
├── Models/                 → User, Invitation, Template, Guest, dll
resources/views/
├── layouts/
│   ├── admin.blade.php     → macOS-style admin layout
│   └── client.blade.php    → macOS-style client layout
├── admin/                  → Admin panel views
├── client/                 → Client panel views
├── auth/                   → Login & Register
└── invitations/
    ├── show.blade.php      → Legacy fallback
    └── templates/          → Multi-template system
routes/
├── web.php                 → Public + auth routes
├── admin.php               → Admin routes (middleware: auth + role:admin)
└── client.php              → Client routes (middleware: auth + role:client)
```

---

## 📸 Screenshot

> Jalankan aplikasi untuk melihat tampilan secara langsung. UI mendukung **Dark Mode** dan **Light Mode** dengan toggle di topbar.

**Auth Pages**
- Login: `http://127.0.0.1:8000/login`
- Register: `http://127.0.0.1:8000/register`

**Demo Undangan**
- Wedding Elegant: `http://127.0.0.1:8000/inv/ahmad-siti`
- Wedding Rustic: `http://127.0.0.1:8000/inv/reza-amelia`
- Birthday Fun: `http://127.0.0.1:8000/inv/birthday-aisyah`
- Wedding Minimalist: `http://127.0.0.1:8000/inv/daniel-sarah`

> ⚠️ Demo undangan baru tersedia setelah menjalankan `SampleInvitationSeeder`

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](LICENSE).

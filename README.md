# 💌 InvitePro — Sistem Undangan Digital

Platform undangan digital berbasis web yang memungkinkan pembuatan, pengelolaan, dan distribusi undangan online untuk berbagai acara seperti pernikahan, ulang tahun, dan lainnya.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

Perintah menjalankan project:

```bash
# Jalankan dengan Vite dev server
composer dev

# Jalankan tanpa npm run dev
composer start

# Build dulu lalu langsung jalankan server
composer start:build
```

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Tech Stack](#-tech-stack)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [Role & Hak Akses](#-role--hak-akses)
- [Template Undangan & Builder CMS](#-template-undangan--builder-cms)
- [Struktur Database](#-struktur-database)
- [Struktur Folder](#-struktur-folder)
- [Screenshot](#-screenshot)
- [Lisensi](#-lisensi)

---

## ✨ Fitur

### 👑 Administrator
- Dashboard statistik (total undangan, tamu, RSVP, template aktif)
- Manajemen User (Client & Guest) — CRUD
- Manajemen Template Undangan — CRUD (Mendukung mode *Legacy Blade* & *Builder CMS*)
- Manajemen Paket / Pricing — CRUD
- Approve / Reject undangan dari client
- **Integrasi Notifikasi & Gateway Pihak Ketiga**:
  - Integrasi Bot Telegram (Konfigurasi & Webhook)
  - Integrasi WhatsApp Gateway (WeaGate API)
- Manajemen Saldo Client (Balance) untuk pengiriman notifikasi
- Pengaturan sistem (nama app, email, logo, dll)

### 🧑‍💼 Client (Pemilik Acara)
- Register & Login
- Pilih template undangan (*Legacy Blade* atau *Builder CMS* dynamic template)
- Input data acara (nama mempelai, tanggal, lokasi, dll)
- Upload foto / galeri
- Kelola daftar tamu & Kirim link undangan personal per tamu
- Pantau RSVP & ucapan
- **Fitur Wedding Planner**:
  - Onboarding Wizard untuk setup detail rencana pernikahan
  - AI Wedding Advisor (Asisten AI konsultasi persiapan pernikahan)
  - Checklist Tugas Pernikahan (To-Do List Terstruktur)
  - Tracker Anggaran & Pengeluaran (Budget Planner)
  - Manajemen Vendor Pernikahan (CRUD & Leads Vendor)
- **Kolaborasi & Backup**:
  - Kolaborator undangan (mengundang user lain untuk ikut mengedit/mengelola)
  - Backup & Restore data undangan secara cepat
- **Analitik Kunjungan (Invitation Funnel)**:
  - Pelacakan metrik/statistik pengunjung secara lengkap (page views, clicks, RSVP)

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
- **macOS-inspired UI** — translucent blur sidebar, rounded corners, Inter font untuk Admin & Client panel
- **Multi-template & Dynamic Builder Mode**:
  - Customizer warna tema, font keluarga (Playfair Display, Cormorant Garamond, Lora, Inter, Plus Jakarta Sans, Manrope), spacing, dan border-radius.
  - Section management: Mengaktifkan, mematikan, mengurutkan, dan memilih varian layout untuk setiap section (Hero, Couple, Events, Gallery, Love Story, RSVP, Wishes, Map, Footer).

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 3.x, Alpine.js |
| Database | MySQL 8.0 |
| Auth | Laravel Breeze |
| Integrations | Telegram Bot API, WeaGate (WhatsApp API) |
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
composer start
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
        Client Register → Pilih Template → Buat Undangan & Setup Wedding Planner
                          ↓
        Admin Approve → Undangan Aktif → Share Link ke Tamu
                          ↓
        Tamu Akses → RSVP & Kirim Ucapan → Pantau via Dashboard & Telegram/WA
```

---

## 🔐 Role & Hak Akses

| Role | Prefix Route | Akses |
|------|-------------|-------|
| **Admin** | `/admin/*` | Full akses ke semua fitur manajemen, integrasi, & template |
| **Client** | `/client/*` | Buat & kelola undangan sendiri, akses Wedding Planner, Kolaborator, & Backup |
| **Guest** | `/inv/{slug}` | View undangan, RSVP, kirim ucapan |

Middleware `RoleMiddleware` di `app/Http/Middleware/RoleMiddleware.php` mengatur akses berdasarkan role.

---

## 🎨 Template Undangan & Builder CMS

Sistem mendukung multi-template dengan dua mode rendering: **Legacy Blade** dan **Builder CMS Engine**.

### 1. Legacy Blade
Setiap template berupa file Blade mandiri yang terletak di:
```
resources/views/invitations/templates/
├── wedding-elegant/index.blade.php     → Gold/dark, partikel emas, shimmer text
├── wedding-rustic/index.blade.php      → Earthy brown, dekorasi daun, serif font
├── birthday-fun/index.blade.php        → Colorful confetti, emoji, Pacifico font
├── wedding-minimalist/index.blade.php  → Light mode B&W, Bodoni Moda, ultra-clean
├── wedding-peach/index.blade.php       → Fresh peach peach accents, romantic
├── wedding-gnv1/index.blade.php        → Custom modern rose-colored template
└── wedding-gnv2/index.blade.php        → Custom clean layout with blue/dark tones
```

### 2. Builder CMS Engine
Mesin render dinamis yang merender undangan berdasarkan tata letak komponen (*sections*) yang dikonfigurasi melalui database. Contoh template bawaan:
- **Wedding Builder Atelier** (`wedding-builder-atelier`): Template adaptif dengan layout *GNV2 Signature* yang bisa dikustomisasi penuh dari Admin Panel.

#### Cara Kerja Builder CMS
Builder CMS menggunakan `TemplateRenderService` untuk menginterpretasi data JSON di kolom `builder_config` yang memuat:
- **Theme**: Mengontrol warna primer, sekunder, aksen, background, teks, font (heading & body), serta pilihan spacing & border-radius.
- **Sections**: Menyusun urutan, mengaktifkan/menonaktifkan, dan memilih varian tata letak untuk komponen berikut:
  - `hero` (Varian: `cover-centered`, `cover-split`)
  - `couple` (Varian: `portrait-stack`, `side-by-side`)
  - `event_schedule` (Varian: `cards`, `timeline`)
  - `gallery` (Varian: `mosaic`, `grid`)
  - `love_story` (Varian: `timeline`, `cards`)
  - `gift` (Varian: `cards`, `minimal`)
  - `rsvp` (Varian: `panel`, `split`)
  - `wishes` (Varian: `feed`, `cards`)
  - `map` (Varian: `card`, `split`)
  - `footer` (Varian: `simple`, `signature`)

---

## 🗄 Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Users dengan role (admin, client, guest) & kolom `balance` (saldo notifikasi) |
| `packages` | Paket harga (Basic, Premium, Exclusive) |
| `client_package_subscriptions` | Data langganan paket client |
| `templates` | Template undangan dengan `render_mode`, `builder_config`, & `builder_layout` |
| `invitations` | Data undangan utama |
| `invitation_backups` | File cadangan data undangan client |
| `invitation_collaborators` | Hubungan otorisasi kolaborator undangan |
| `invitation_funnel_events` | Log event analitik kunjungan undangan |
| `invitation_photos` | Foto/galeri undangan |
| `invitation_events` | Sub-event (akad, resepsi, dll) |
| `guests` | Daftar tamu dengan link unik |
| `rsvps` | Konfirmasi kehadiran |
| `wishes` | Ucapan & doa dari tamu |
| `invitation_views` | Tracking kunjungan dasar |
| `payments` | Data pembayaran langganan & saldo |
| `settings` | Pengaturan aplikasi global |
| **Wedding Planner Tables** | Berisi: `wp_profiles` (data mempelai planner), `wp_checklists` (checklist tugas), `wp_budgets` & `wp_budget_items` (manajemen keuangan), `wp_vendors` (manajemen vendor), `wp_timeline_events`, dan `wp_advisor_logs` (log AI Advisor). |

---

## 📁 Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          → DashboardController, UserController, IntegrationController (Telegram/WA/Email), dll
│   │   ├── Client/         → DashboardController, InvitationController, Planner/ (Advisor, Budget, Checklist, Onboarding, Vendor), dll
│   │   └── Auth/           → RegisteredUserController (first = admin)
│   └── Middleware/
│       ├── EnsureActiveClientPackage.php
│       ├── RoleMiddleware.php
│       └── TrackInvitationView.php
├── Models/                 → User, Invitation, Template, Guest, ClientPackageSubscription, Planner/ (WpProfile, WpChecklistItem, WpBudgetCategory, WpBudgetItem, WpVendor, WpTimelineEvent, WpAdvisorLog), dll
├── Services/               → TemplateRenderService, TelegramService, WeaGateService, PhoneNormalizerService, Planner/ (PlannerOnboardingService, WeddingAdvisorService), dll
resources/views/
├── layouts/
│   ├── admin.blade.php     → macOS-style admin layout
│   └── client.blade.php    → macOS-style client layout
├── admin/                  → Admin panel views (integration/, templates/, dll)
├── client/                 → Client panel views (planner/, packages/, invitations/, dll)
├── auth/                   → Login & Register
└── invitations/
    ├── show.blade.php      → Legacy fallback
    ├── builder/            → Dynamic Builder/CMS views & sections (hero, couple, gallery, rsvp, dll)
    └── templates/          → Multi-template system (wedding-elegant, wedding-rustic, birthday-fun, wedding-minimalist, wedding-peach, wedding-gnv1, wedding-gnv2)
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

> ⚠️ Demo undangan baru tersedia setelah menjalankan `SampleInvitationSeeder`

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](LICENSE).

# Multi-Package Subscription Implementation Plan

## Summary
Dokumen ini menjelaskan rencana implementasi perubahan arsitektur paket client dari model `single active package` menjadi `multiple active package subscriptions`.

Target bisnis yang ingin dicapai:
- Satu client dapat membeli lebih dari satu paket langganan.
- Setiap pembelian paket memiliki kuota undangan sendiri.
- Setiap paket memiliki aturan akses template sendiri, termasuk template premium.
- Setiap undangan harus terikat ke paket pembelian tertentu.
- Halaman `Undangan Saya` menampilkan undangan yang dikelompokkan berdasarkan paket yang dibeli client.

Contoh target hasil akhir:
- Client membeli `Starter` dan `Growth`.
- Client dapat membuat undangan menggunakan jatah `Starter` selama kuotanya masih tersedia.
- Client juga dapat membuat undangan menggunakan jatah `Growth` secara terpisah.
- Pada halaman `Undangan Saya`, client melihat card `Starter` dan `Growth`.
- Saat card dibuka, muncul daftar undangan yang dibuat menggunakan paket tersebut.

## Current State
Arsitektur yang aktif saat ini masih berbasis satu paket aktif global per user.

Temuan utama:
- `ClientPackageService::getActiveSubscription()` hanya mengambil satu subscription aktif terbaru.
- `ClientPackageService::activateFromPayment()` mengubah semua subscription aktif lain menjadi `expired`.
- `InvitationController::create()` dan `InvitationController::store()` selalu memakai satu `active package` global.
- Kuota undangan dihitung berdasarkan total semua undangan user, bukan berdasarkan paket pembelian tertentu.
- `invitations.package_id` sudah tersedia, tetapi belum cukup untuk membedakan dua pembelian paket yang sama.
- Halaman `client.invitations.index` masih menampilkan daftar undangan flat, belum grouped per paket/subscription.

Konsekuensi dari desain saat ini:
- User tidak bisa memiliki dua paket aktif sekaligus.
- User tidak bisa membeli paket yang sama dua kali dan memakai kuotanya secara terpisah.
- Sistem tidak bisa mengetahui sebuah undangan memakai kuota dari pembelian paket yang mana.

## Business Goal
Perubahan sistem harus mendukung model berikut:

1. Satu user dapat memiliki banyak `client_package_subscriptions` aktif sekaligus.
2. Setiap subscription mewakili satu pembelian paket yang berdiri sendiri.
3. Kuota undangan dihitung per subscription, bukan per user global.
4. Hak akses template ditentukan oleh paket pada subscription yang dipilih saat membuat undangan.
5. Invitation harus menyimpan referensi ke subscription yang dipakai saat invitation dibuat.
6. Invitation yang sudah dibuat tetap terikat ke subscription asalnya, walaupun user membeli paket baru setelah itu.

## Core Design Decision
### 1. Subscription menjadi unit utama pemakaian kuota
Setiap pembelian paket akan menghasilkan satu record `client_package_subscriptions`.

Implikasi:
- Jika user membeli `Starter` dua kali, maka akan ada dua subscription berbeda.
- Masing-masing subscription punya kuota sendiri.
- Invitation harus menunjuk ke subscription tertentu, bukan hanya ke jenis paket.

### 2. `package_id` tidak cukup, perlu `client_package_subscription_id`
Saat ini `invitations` hanya menyimpan `package_id`.

Masalah:
- Jika ada dua subscription `Starter`, kedua invitation akan terlihat sama-sama memakai `Starter`, tetapi sistem tidak tahu jatah starter yang mana yang terpakai.

Solusi:
- Tambahkan kolom `client_package_subscription_id` ke tabel `invitations`.
- `package_id` tetap dipertahankan untuk snapshot data dan kompatibilitas existing code.
- Source of truth untuk kuota adalah `client_package_subscription_id`.

### 3. Invitation harus stabil terhadap perubahan paket user
Invitation yang dibuat dari subscription tertentu tidak boleh bergantung pada `active package terbaru`.

Implikasi:
- Saat edit, publish, dan pengecekan akses, sistem harus memakai package dari invitation/subscription tersebut.
- Membeli paket baru tidak boleh mengubah hak template atau kuota invitation lama secara otomatis.

## Scope of Changes
Perubahan mencakup:
- database schema
- model relation
- service layer
- middleware pembatas create invitation
- alur pembelian paket
- alur create invitation
- halaman list invitation client
- validasi template premium dan template allowlist
- publish/activate invitation flow
- pengelompokan usage per paket
- testing

Perubahan ini tidak wajib langsung mencakup:
- migrasi invitation lama antar paket secara manual
- merge beberapa pembelian paket sejenis menjadi satu subscription gabungan
- downgrade otomatis
- bundling kuota antar paket

## Data Model Changes
### 1. Tabel `invitations`
Tambahkan:
- `client_package_subscription_id` nullable pada tahap transisi

Relasi:
- `invitations.client_package_subscription_id -> client_package_subscriptions.id`

Tujuan:
- Menyimpan pembelian paket spesifik yang dipakai oleh invitation.

Catatan transisi:
- Untuk data lama, `client_package_subscription_id` bisa diisi belakangan lewat migration backfill jika memungkinkan.
- Jika backfill tidak bisa akurat 100%, record lama perlu fallback logic sementara.

### 2. Tabel `client_package_subscriptions`
Tidak perlu perubahan besar pada struktur dasar, tetapi perubahan logika diperlukan:
- Tidak lagi memaksa satu subscription aktif per user.
- Status `active` dapat dimiliki oleh banyak record sekaligus selama belum expired.

Opsional enhancement:
- Tambah `label` atau `display_name` jika nanti perlu membedakan pembelian paket sejenis di UI.
- Tambah `meta` JSON jika nanti perlu menyimpan snapshot rules paket pada saat pembelian.

### 3. Tabel `payments`
Tidak wajib berubah untuk fase awal, karena relasi ke `client_package_subscription_id` sudah ada.

## Domain Rules
### Rule 1: Active subscription
Subscription dianggap usable jika:
- `status = active`
- `expires_at` null atau `expires_at > now()`

### Rule 2: Invitation quota
Kuota invitation dihitung dari:
- `jumlah invitation yang memakai subscription tersebut`
dibandingkan dengan:
- `subscription.package.max_invitations`

### Rule 3: Template access
Template yang boleh dipakai ditentukan oleh package pada subscription:
- jika `allowed_template_ids` kosong, semua template boleh
- jika tidak kosong, template harus ada di allowlist
- jika template premium, package juga harus memenuhi rule premium access

### Rule 4: Invitation ownership
Setiap invitation hanya boleh memakai satu subscription.

### Rule 5: Stable package binding
Setelah invitation dibuat:
- package binding tidak berubah otomatis
- validasi edit/publish mengikuti package binding tersebut

## Service Layer Refactor
### `ClientPackageService`
Service ini perlu diubah dari model single active package ke multi-subscription manager.

Method lama yang perlu di-review:
- `getActiveSubscription(int $userId)`
- `getActivePackage(int $userId)`
- `canCreateInvitation(int $userId)`
- `activateFromPayment(Payment $payment)`

Method baru yang disarankan:
- `getActiveSubscriptions(int $userId): Collection`
- `getUsableSubscriptions(int $userId): Collection`
- `getSubscriptionUsage(ClientPackageSubscription $subscription): array`
- `getSubscriptionUsageSummary(int $subscriptionId): array`
- `canUseSubscriptionForTemplate(int $userId, int $subscriptionId, int $templateId): array`
- `canCreateInvitationWithSubscription(int $userId, int $subscriptionId, int $templateId): array`
- `findAuthorizedUsableSubscription(int $userId, int $subscriptionId): ?ClientPackageSubscription`
- `getInvitationPackageContext(Invitation $invitation): ?ClientPackageSubscription`

Perubahan penting pada `activateFromPayment()`:
- hapus logic yang meng-expire semua subscription aktif lain
- hanya aktifkan subscription yang dibayar
- set `started_at` dan `expires_at` untuk subscription yang relevan

### `InvitationAccess / Package Validation`
Validasi paket tidak lagi boleh memakai global active package.

Harus dipindah ke rule berbasis:
- invitation subscription
- invitation package snapshot

## Controller Changes
### 1. `ClientPackageController`
Perubahan yang diperlukan:
- `select()` menampilkan daftar paket seperti sekarang
- `store()` tetap membuat satu pending subscription per pembelian
- `checkoutProcess()` tetap mengaktifkan subscription yang dibayar
- hapus asumsi bahwa subscription aktif hanya satu

Tambahan opsional:
- tampilkan daftar paket yang sudah dimiliki user dan usage masing-masing

### 2. `InvitationController::index()`
Saat ini:
- mengambil semua invitation dengan pagination flat

Target:
- mengambil daftar subscription aktif milik user
- eager load package dan invitations untuk masing-masing subscription
- kelompokkan invitation per subscription
- tampilkan juga subscription aktif yang belum punya invitation

Data yang perlu dikirim ke view:
- `subscriptions`
- `subscriptionUsage`
- `ungroupedLegacyInvitations` bila ada data lama tanpa subscription binding
- `hasUsableSubscription`

### 3. `InvitationController::create()`
Saat ini:
- langsung mengambil satu active package global

Target:
- halaman create harus mengenali subscription yang dipilih user
- user memilih package subscription yang akan dipakai
- template yang tampil difilter berdasarkan subscription tersebut

Opsi flow yang paling disarankan:
- user masuk halaman create dengan parameter `subscription_id`
- controller validasi subscription milik user dan masih usable
- controller hanya menampilkan template yang bisa dipakai subscription tersebut

Fallback:
- jika `subscription_id` belum dipilih, tampilkan langkah pemilihan paket dulu

### 4. `InvitationController::store()`
Validasi request perlu ditambah:
- `client_package_subscription_id` required

Langkah validasi:
1. Pastikan subscription milik user.
2. Pastikan subscription aktif dan belum expired.
3. Pastikan template boleh untuk package subscription itu.
4. Pastikan quota subscription belum habis.

Saat save invitation:
- simpan `client_package_subscription_id`
- simpan `package_id` dari subscription package

### 5. `InvitationController::show()`
Saat ini beberapa limit membaca dari `activePackage` global atau fallback `invitation->package`.

Target:
- gunakan package dari subscription yang menempel ke invitation
- jika subscription tidak tersedia, fallback ke `invitation->package` hanya untuk legacy compatibility

### 6. `InvitationController::edit()` dan `update()`
Validasi template saat update harus berbasis package binding invitation, bukan package global user.

Catatan:
- Untuk fase awal, sebaiknya invitation tidak boleh pindah subscription lewat form edit.
- Jika nanti ingin mendukung pindah paket, itu dibuat flow terpisah.

### 7. `InvitationController::toggleStatus()` dan `submit()`
Saat publish/activate:
- jangan gunakan `getActivePackage(auth()->id())`
- gunakan subscription/package yang menempel di invitation
- validasi bahwa subscription tersebut masih usable jika aturan bisnis mengharuskan paket tetap aktif saat publish

Keputusan bisnis yang perlu dijaga:
- bila subscription expired, apakah invitation tetap boleh aktif atau tidak
- untuk fase awal, rekomendasi paling aman: publish/activate butuh subscription masih aktif

## Middleware Changes
### `EnsureActiveClientPackage`
Saat ini middleware hanya cek `canCreateInvitation(userId)` tanpa konteks subscription.

Masalah:
- create invitation sekarang membutuhkan subscription spesifik

Rekomendasi:
- middleware lama tidak cukup lagi sebagai source of truth
- logic utama pindah ke controller create/store

Opsi implementasi:
- pertahankan middleware hanya untuk cek apakah user punya minimal satu subscription usable
- validasi rinci tetap di controller berdasarkan `subscription_id`

## View / UX Changes
### 1. Halaman `Pilih Paket`
Perlu tetap mendukung pembelian paket tambahan.

Tambahan UX yang disarankan:
- tandai paket yang sudah pernah dibeli
- tampilkan jumlah subscription aktif per jenis paket
- tampilkan total kuota terpakai per pembelian jika perlu

### 2. Halaman `Buat Undangan`
Perlu berubah menjadi flow berbasis subscription.

Rekomendasi UX:
- user klik `Buat Undangan` dari card paket pada halaman `Undangan Saya`
- route membawa `subscription_id`
- halaman create menampilkan info package context:
  - nama paket
  - sisa kuota
  - template yang tersedia

Ini lebih aman daripada membiarkan user memilih paket di tengah form panjang.

### 3. Halaman `Undangan Saya`
Target UI:
- daftar card per subscription
- setiap card menampilkan:
  - nama paket
  - status aktif/expired
  - kuota terpakai dan sisa kuota
  - daftar fitur/template access ringkas
  - tombol `Buat Undangan` untuk subscription itu
  - tombol expand/collapse
- saat card dibuka, tampil daftar invitation milik subscription itu

Jika user membeli dua paket dengan nama sama:
- tetap tampil sebagai dua card terpisah
- gunakan informasi pembelian seperti tanggal beli atau nomor subscription untuk pembeda

Contoh display:
- `Starter - Pembelian 28 Mei 2026`
- `Starter - Pembelian 30 Mei 2026`

### 4. Legacy invitations section
Jika ada invitation lama yang belum punya `client_package_subscription_id`, tampilkan section khusus:
- `Undangan Lama`
- hanya untuk masa transisi sampai backfill selesai

## Implementation Phases
## Phase 1: Schema and domain foundation
- Tambah kolom `client_package_subscription_id` pada tabel `invitations`
- Tambah foreign key dan index
- Tambah relasi model:
  - `Invitation belongsTo ClientPackageSubscription`
  - `ClientPackageSubscription hasMany Invitation`
- Update service dasar untuk membaca banyak subscription aktif

Deliverable:
- codebase siap mengenali invitation per subscription

## Phase 2: Multi-subscription activation
- Ubah `ClientPackageService::activateFromPayment()`
- Hapus auto-expire subscription aktif lain
- Pastikan checkout paket hanya mengaktifkan subscription yang dibeli

Deliverable:
- satu user bisa punya banyak subscription aktif

## Phase 3: Invitation creation refactor
- Ubah `InvitationController::create()` agar memakai `subscription_id`
- Ubah `InvitationController::store()` agar validasi quota/template berdasarkan subscription
- Simpan `client_package_subscription_id` ke invitation

Deliverable:
- invitation baru selalu terikat ke subscription yang benar

## Phase 4: Invitation management stability
- Ubah `show`, `edit`, `update`, `submit`, `toggleStatus`
- Hilangkan ketergantungan ke global active package
- Pastikan semua limit membaca package binding invitation

Deliverable:
- invitation existing baru bekerja stabil per paket pembelian

## Phase 5: My Invitations grouped UI
- Refactor query dan view `client.invitations.index`
- Tampilkan card grouped per subscription
- Tambahkan expand/collapse invitation list
- Tambahkan CTA `Buat Undangan` per card

Deliverable:
- user melihat portfolio invitation berdasarkan paket yang dibeli

## Phase 6: Legacy data handling
- Tambah strategi backfill `client_package_subscription_id` untuk invitation lama
- Jika tidak bisa akurat, tambahkan fallback UI/logic transisi

Deliverable:
- data lama tetap bisa diakses tanpa merusak alur baru

## Backfill Strategy for Existing Data
Masalah utama:
- invitation lama hanya punya `package_id`
- user mungkin punya lebih dari satu subscription dengan package yang sama setelah fitur baru aktif

Strategi minimum:
1. Untuk user yang hanya punya satu subscription relevan terhadap `package_id`, isi otomatis.
2. Untuk invitation yang ambigu, biarkan `client_package_subscription_id = null`.
3. Invitation legacy tetap bisa dibuka dengan fallback ke `package_id`.
4. Invitation baru wajib memakai `client_package_subscription_id`.

Strategi lanjutan opsional:
- buat command admin/manual resolver untuk assign invitation legacy ke subscription tertentu

## Risks
### 1. Ambiguitas data lama
Invitation lama tidak punya ikatan ke subscription spesifik.

Mitigasi:
- gunakan nullable transition
- fallback logic sementara
- sediakan mekanisme backfill/manual assignment jika diperlukan

### 2. Paket sama dibeli berkali-kali
Jika user membeli paket identik berkali-kali, UI bisa membingungkan.

Mitigasi:
- tampilkan tanggal pembelian atau ID subscription singkat di card

### 3. Logic publish lama masih global
Beberapa method existing masih memakai `getActivePackage()`.

Mitigasi:
- audit semua pemakaian `getActivePackage()` dan `getActiveSubscription()`
- ubah semua logic yang menyentuh quota/template/publish

### 4. Regression pada template validation
Jika validasi template tidak dipindah dengan benar, user bisa memakai template di luar hak paket.

Mitigasi:
- tambahkan test per package-template combination

### 5. Middleware create menjadi misleading
Jika middleware tetap terlalu sederhana, user bisa lolos ke halaman create tetapi gagal saat submit.

Mitigasi:
- middleware hanya sebagai guard ringan
- semua validasi final tetap di controller/service

## Detailed Test Plan
### Subscription tests
- User membeli satu paket dan subscription aktif.
- User membeli dua paket berbeda dan keduanya aktif bersamaan.
- User membeli paket yang sama dua kali dan keduanya aktif bersamaan.
- Aktivasi subscription baru tidak meng-expire subscription aktif lama.

### Invitation creation tests
- User dengan satu subscription aktif bisa membuat invitation memakai subscription tersebut.
- User dengan dua subscription aktif bisa memilih subscription saat create.
- Invitation tersimpan dengan `client_package_subscription_id` yang benar.
- Quota dihitung hanya dari invitation pada subscription yang sama.
- Subscription A penuh tidak boleh memblokir create pada subscription B yang masih punya kuota.

### Template access tests
- Template premium ditolak jika package subscription tidak mengizinkan.
- Template premium diterima jika package subscription mengizinkan.
- Template allowlist dihormati per subscription.

### Invitation management tests
- Edit invitation memakai rule package dari subscription asal.
- Publish invitation memakai package binding yang benar.
- Membeli paket baru tidak mengubah package binding invitation lama.

### UI tests
- Halaman `Undangan Saya` menampilkan card semua subscription aktif.
- Subscription tanpa invitation tetap tampil.
- Expand card menampilkan invitation yang hanya milik subscription itu.
- Dua subscription dengan package name sama tetap bisa dibedakan.

### Legacy tests
- Invitation lama tanpa `client_package_subscription_id` tetap bisa dibuka.
- Fallback legacy tidak merusak invitation baru.

## Suggested File Impact
Kemungkinan file yang akan terdampak:
- `app/Services/ClientPackageService.php`
- `app/Models/ClientPackageSubscription.php`
- `app/Models/Invitation.php`
- `app/Http/Controllers/Client/ClientPackageController.php`
- `app/Http/Controllers/Client/InvitationController.php`
- `app/Http/Middleware/EnsureActiveClientPackage.php`
- `resources/views/client/invitations/index.blade.php`
- `resources/views/client/invitations/create.blade.php`
- `routes/client.php`
- migration baru untuk `invitations.client_package_subscription_id`

## Recommended Delivery Order
1. Tambah schema dan relation baru.
2. Ubah activation logic agar multi-subscription aktif.
3. Ubah create/store invitation ke subscription-specific flow.
4. Ubah show/edit/publish agar tidak lagi bergantung ke global active package.
5. Ubah halaman `Undangan Saya` menjadi grouped per subscription.
6. Tambah fallback legacy dan test coverage.

## Assumptions
- Satu pembelian paket = satu subscription terpisah.
- Subscription berbeda tidak menggabungkan kuota walaupun package sama.
- Invitation baru wajib memilih satu subscription saat dibuat.
- Invitation yang sudah dibuat tetap terikat ke subscription asalnya.
- Untuk fase awal, pemindahan invitation antar subscription belum didukung.
- Untuk fase awal, publish invitation memerlukan subscription asal masih valid/usable.

## Success Criteria
Implementasi dianggap berhasil jika:
- User dapat memiliki lebih dari satu paket aktif secara bersamaan.
- User dapat membuat invitation dari paket yang berbeda secara terpisah.
- Quota invitation dihitung akurat per subscription.
- Template access mengikuti paket subscription yang dipilih.
- Halaman `Undangan Saya` menampilkan grouping invitation per paket pembelian.
- Invitation lama tetap bisa diakses selama masa transisi.

# Rencana Implementasi: Fitur Transfer Manual Payment

## Latar Belakang & Tujuan

Saat ini sistem hanya mendukung dua metode pembayaran:
1. **Payment Gateway** (Xendit / Tripay) – diproses otomatis via middleware
2. **Potong Saldo (Balance)** – langsung dipotong dari dompet client

Fitur baru yang akan ditambahkan:
- **Transfer Manual** — Client memilih metode ini, mendapat nomor rekening tujuan, melakukan transfer, lalu mengunggah bukti transfer. Admin mereview dan mengonfirmasi. Sistem kemudian mengirim notifikasi otomatis ke Telegram.

---

## Gambaran Besar Alur Sistem

```
[ADMIN]
  1. Tentukan metode payment yang aktif:
     └── pilih salah satu atau keduanya:
         ├── payment_gateway (Xendit/Tripay)
         └── transfer_manual
  2. Jika transfer_manual aktif, isi data rekening bank tujuan
     (bisa lebih dari 1 rekening)

[CLIENT - CHECKOUT]
  1. Halaman checkout menampilkan opsi metode bayar yang aktif
  2. Jika memilih "Transfer Manual":
     ├── Sistem membuat Payment record status = pending_verification
     ├── Client ditampilkan info rekening tujuan + nominal
     ├── Client meng-copy nomor rekening (tombol copy)
     ├── Setelah transfer → client upload bukti transfer (foto)
     └── Sistem kirim notifikasi Telegram:
         "🔔 Ada pengajuan bukti transfer manual baru dari [nama client]"

[ADMIN - REVIEW]
  1. Admin melihat daftar pembayaran manual yang menunggu konfirmasi
  2. Admin bisa:
     ├── APPROVE → status berubah jadi paid, undangan aktif
     │   Telegram: "✅ Transfer Manual dikonfirmasi untuk [client]"
     └── REJECT  → status berubah jadi failed, client dinotifikasi
         Telegram: "❌ Transfer Manual ditolak untuk [client]"
```

---

## Detail Fitur & Logik

### A. Pengaturan Admin

#### A1. Pilih Metode Payment Aktif

Di halaman **Integrasi → Payment Gateway**, tambahkan section baru:

> **Metode Pembayaran yang Aktif**
>
> Pilihan (bisa multi-select atau single pilih per grup):
> - ☑ Payment Gateway (Xendit / Tripay)
> - ☑ Transfer Manual

Setting keys baru di tabel `settings`:

| Key | Tipe | Deskripsi |
|---|---|---|
| `payment_method_gateway` | `1/0` | Apakah payment gateway aktif |
| `payment_method_transfer_manual` | `1/0` | Apakah transfer manual aktif |

#### A2. Konfigurasi Rekening Bank

Form untuk menambah/mengedit rekening tujuan. Disimpan di tabel baru `manual_transfer_bank_accounts`:

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | |
| `bank_name` | string | Nama bank (BCA, BRI, dll) |
| `account_number` | string | Nomor rekening |
| `account_holder_name` | string | Nama pemilik rekening |
| `is_active` | boolean | Status aktif |
| `sort_order` | integer | Urutan tampil |
| `created_at` / `updated_at` | timestamp | |

#### A3. Halaman Konfirmasi Transfer Manual (Admin)

Di halaman **Admin → Manual Transfer**, ada daftar:
- Pembayaran dengan `payment_method = 'transfer_manual'` dan `payment_status = 'pending_verification'`
- Setiap item: nama client, nominal, tanggal submit, preview foto bukti
- Tombol **Konfirmasi** dan **Tolak**

---

### B. Alur Client

#### B1. Halaman Checkout (Pilih Metode)

Checkout akan memiliki selector metode pembayaran, tampil sesuai yang diaktifkan admin:

```
┌─────────────────────────────────────────────┐
│  Pilih Metode Pembayaran                    │
│                                             │
│  ○ Payment Gateway (QRIS / E-Wallet)        │
│  ○ Transfer Manual ke Rekening Bank         │
└─────────────────────────────────────────────┘
```

Jika admin hanya mengaktifkan satu metode, selector tidak ditampilkan dan langsung pakai metode tersebut.

#### B2. Halaman Instruksi Transfer

Setelah memilih Transfer Manual dan submit, client diarahkan ke halaman instruksi:

```
┌──────────────────────────────────────────────┐
│  Instruksi Transfer Manual                   │
│                                              │
│  Silakan transfer ke rekening berikut:       │
│                                              │
│  🏦 Bank BCA                                 │
│  Nomor Rekening: 1234567890  [Copy]          │
│  Atas Nama: PT. Undangan Digital             │
│                                              │
│  Total yang harus ditransfer:                │
│  Rp 150.000                                  │
│                                              │
│  ─────────────────────────────────────       │
│  Upload Bukti Transfer                       │
│                                              │
│  [Pilih Foto]  (jpg/png, maks 5MB)           │
│                                              │
│  [Kirim Bukti Transfer]                      │
└──────────────────────────────────────────────┘
```

#### B3. Setelah Upload

Setelah upload berhasil:
- Payment status → `pending_verification`
- Halaman status menampilkan: "Bukti transfer Anda sudah kami terima. Mohon tunggu konfirmasi dari admin (biasanya dalam 1×24 jam)."
- Telegram notification dikirim ke admin group

---

### C. Notifikasi Telegram

#### C1. Saat Client Upload Bukti Transfer
```
🔔 Bukti Transfer Manual Diterima!
──────────────────
👤 Client   : [Nama Client]
📧 Email    : [email]
💰 Nominal  : Rp[amount]
📦 Invoice  : [invoice_number]
🕐 Waktu    : [timestamp]

Silakan konfirmasi di panel admin.
```

#### C2. Saat Admin Konfirmasi (Approve)
```
✅ Transfer Manual Dikonfirmasi!
──────────────────
👤 Client   : [Nama Client]
💰 Nominal  : Rp[amount]
📦 Invoice  : [invoice_number]
🛡️ Admin    : [Nama Admin yang konfirmasi]
🕐 Waktu    : [timestamp]
```

#### C3. Saat Admin Tolak (Reject)
```
❌ Transfer Manual Ditolak
──────────────────
👤 Client   : [Nama Client]
💰 Nominal  : Rp[amount]
📦 Invoice  : [invoice_number]
📝 Alasan   : [alasan penolakan]
🕐 Waktu    : [timestamp]
```

---

## Status Payment Baru

Di model `Payment`, tambahkan constant baru:

```php
public const STATUS_PENDING_VERIFICATION = 'pending_verification';
```

Flow status untuk Transfer Manual:
```
(create order) → pending_verification → paid    (jika admin approve)
                                      → failed  (jika admin reject)
```

---

## File yang Perlu Dibuat / Dimodifikasi

### Database

#### [NEW] Migration: `create_manual_transfer_bank_accounts_table`
Buat tabel `manual_transfer_bank_accounts`

#### [NEW] Migration: `add_transfer_fields_to_payments_table`
Tambahkan kolom ke tabel `payments`:
- `transfer_proof_path` — string nullable (path foto bukti transfer)
- `transfer_verified_at` — timestamp nullable
- `transfer_verified_by` — unsignedBigInteger nullable (FK ke users)
- `transfer_rejection_reason` — text nullable

---

### Models

#### [NEW] `app/Models/ManualTransferBankAccount.php`
Model untuk tabel rekening bank transfer manual.

#### [MODIFY] `app/Models/Payment.php`
- Tambah `STATUS_PENDING_VERIFICATION = 'pending_verification'`
- Tambah kolom ke `$fillable`
- Tambah method `isPendingVerification()`, `isManualTransfer()`
- Tambah method `markAsVerified()`, `markAsRejected(string $reason)`
- Tambah relasi `verifiedBy()` → BelongsTo User

---

### Services

#### [MODIFY] `app/Services/TelegramNotificationService.php`
Tambah 3 method baru:
- `manualTransferProofSubmitted(Payment $payment): void`
- `manualTransferConfirmed(Payment $payment, User $admin): void`
- `manualTransferRejected(Payment $payment, User $admin, string $reason): void`

#### [NEW] `app/Services/ManualTransferService.php`
Service khusus untuk logik transfer manual:
- `getActiveBankAccounts(): Collection`
- `processProofSubmission(Payment $payment, UploadedFile $file): bool`
- `confirmTransfer(Payment $payment, User $admin): bool`
- `rejectTransfer(Payment $payment, User $admin, string $reason): bool`

---

### Controllers

#### [NEW] `app/Http/Controllers/Admin/ManualTransferController.php`
- `index()` — daftar pembayaran pending_verification
- `show(Payment $payment)` — detail + foto bukti
- `confirm(Payment $payment)` — approve pembayaran
- `reject(Request $request, Payment $payment)` — tolak dengan alasan
- `bankAccounts()` — daftar rekening bank
- `storeBankAccount(Request $request)` — simpan rekening baru
- `updateBankAccount(Request $request, ManualTransferBankAccount $account)` — update rekening
- `destroyBankAccount(ManualTransferBankAccount $account)` — hapus rekening

#### [MODIFY] `app/Http/Controllers/Client/CheckoutController.php`
- `show()` — sertakan data rekening bank & setting metode aktif ke view
- `processManualTransfer(Request, Invitation)` — buat payment record + redirect ke instruksi
- `submitTransferProof(Request, Invitation)` — upload bukti + notif Telegram

#### [MODIFY] `app/Http/Controllers/Admin/IntegrationController.php`
- `paymentGateway()` — tambahkan setting baru ke config array
- `paymentGatewayUpdate()` — simpan `payment_method_gateway` & `payment_method_transfer_manual`

---

### Routes

#### [MODIFY] `routes/admin.php`
```php
// Manual Transfer Management
Route::get('/manual-transfer', [ManualTransferController::class, 'index'])->name('manual-transfer.index');
Route::get('/manual-transfer/{payment}', [ManualTransferController::class, 'show'])->name('manual-transfer.show');
Route::patch('/manual-transfer/{payment}/confirm', [ManualTransferController::class, 'confirm'])->name('manual-transfer.confirm');
Route::patch('/manual-transfer/{payment}/reject', [ManualTransferController::class, 'reject'])->name('manual-transfer.reject');

// Bank Account Settings
Route::get('/manual-transfer/bank-accounts', [ManualTransferController::class, 'bankAccounts'])->name('manual-transfer.bank-accounts');
Route::post('/manual-transfer/bank-accounts', [ManualTransferController::class, 'storeBankAccount'])->name('manual-transfer.bank-accounts.store');
Route::put('/manual-transfer/bank-accounts/{account}', [ManualTransferController::class, 'updateBankAccount'])->name('manual-transfer.bank-accounts.update');
Route::delete('/manual-transfer/bank-accounts/{account}', [ManualTransferController::class, 'destroyBankAccount'])->name('manual-transfer.bank-accounts.destroy');
```

#### [MODIFY] `routes/client.php`
```php
// Manual Transfer Checkout
Route::post('/checkout/{invitation}/manual-transfer', [CheckoutController::class, 'processManualTransfer'])->name('checkout.manual-transfer.process');
Route::get('/checkout/{invitation}/manual-transfer/instructions', [CheckoutController::class, 'manualTransferInstructions'])->name('checkout.manual-transfer.instructions');
Route::post('/checkout/{invitation}/manual-transfer/proof', [CheckoutController::class, 'submitTransferProof'])->name('checkout.manual-transfer.proof');
```

---

### Views (Admin)

#### [NEW] `resources/views/admin/manual-transfer/index.blade.php`
Daftar pembayaran transfer manual menunggu konfirmasi:
- Filter: pending_verification / semua
- Tabel: nama client, nominal, tanggal, preview thumb foto bukti
- Tombol Konfirmasi & Tolak (dengan modal konfirmasi)

#### [NEW] `resources/views/admin/manual-transfer/show.blade.php`
Detail satu pembayaran transfer manual:
- Info client & payment lengkap
- Preview foto bukti transfer (bisa full-size)
- Form Tolak dengan input alasan

#### [NEW] `resources/views/admin/manual-transfer/bank-accounts.blade.php`
Pengaturan rekening bank tujuan:
- Daftar rekening yang ada (CRUD)
- Form tambah rekening baru
- Toggle aktif/nonaktif

#### [MODIFY] `resources/views/admin/integration/payment-gateway.blade.php`
Tambah section "Metode Pembayaran Aktif":
- Checkbox: Payment Gateway
- Checkbox: Transfer Manual
- Link ke halaman Bank Accounts

---

### Views (Client)

#### [MODIFY] `resources/views/client/checkout/show.blade.php`
Tambahkan selector metode bayar berdasarkan setting yang aktif.

#### [NEW] `resources/views/client/checkout/manual-transfer-instructions.blade.php`
Halaman instruksi transfer:
- Daftar rekening bank aktif (semua ditampilkan)
- Tombol copy nomor rekening (clipboard JS)
- Nominal yang harus ditransfer (highlight besar)
- Form upload foto bukti dengan preview real-time
- Tombol Kirim Bukti Transfer

#### [MODIFY] `resources/views/client/checkout/status.blade.php`
Tambah kondisi untuk status `pending_verification`:
- Ikon jam / pending (warna kuning/orange)
- Pesan menunggu konfirmasi admin
- Info invoice & nominal
- Tombol upload ulang bukti (selama masih pending)

---

### Storage

- Foto bukti transfer disimpan di: `storage/app/public/transfer-proofs/{payment_id}/proof.jpg`
- Akses publik: `/storage/transfer-proofs/{payment_id}/proof.jpg`

---

## Urutan Implementasi

| # | File / Tugas |
|---|---|
| 1 | Migration: create_manual_transfer_bank_accounts_table |
| 2 | Migration: add_transfer_fields_to_payments_table |
| 3 | `php artisan migrate` |
| 4 | Model: ManualTransferBankAccount |
| 5 | Model: Payment (update) |
| 6 | Service: ManualTransferService |
| 7 | Service: TelegramNotificationService (update, tambah 3 method) |
| 8 | Controller: Admin\ManualTransferController |
| 9 | Controller: Admin\IntegrationController (update) |
| 10 | Controller: Client\CheckoutController (update) |
| 11 | Route: admin.php (update) |
| 12 | Route: client.php (update) |
| 13 | View: admin/manual-transfer/index.blade.php |
| 14 | View: admin/manual-transfer/show.blade.php |
| 15 | View: admin/manual-transfer/bank-accounts.blade.php |
| 16 | View: admin/integration/payment-gateway.blade.php (update) |
| 17 | View: client/checkout/show.blade.php (update) |
| 18 | View: client/checkout/manual-transfer-instructions.blade.php |
| 19 | View: client/checkout/status.blade.php (update) |
| 20 | Sidebar admin: tambah menu "Manual Transfer" |

---

## Pertanyaan Terbuka (Perlu Konfirmasi Sebelum Implementasi)

> [!IMPORTANT]
> **1. Apakah Transfer Manual hanya untuk Checkout Undangan?**
> Atau juga untuk Top Up Saldo? Saat ini saldo bisa diisi via payment gateway. Apakah mau menambahkan opsi top up saldo via transfer manual juga?

> [!IMPORTANT]
> **2. Satu atau banyak rekening ditampilkan ke client?**
> Apakah client melihat semua rekening aktif sekaligus dan bebas pilih bank mana yang mau dipakai untuk transfer? Atau client harus memilih bank dulu baru muncul nomor rekeningnya?

> [!NOTE]
> **3. Batas waktu upload bukti transfer?**
> Apakah ada batas waktu (misalnya 24 jam) bagi client untuk upload bukti? Jika ya, payment akan berubah ke `expired` otomatis via scheduled job.

> [!NOTE]
> **4. Notifikasi selain Telegram?**
> Apakah admin perlu menerima notifikasi via email juga, atau cukup Telegram saja?

> [!NOTE]
> **5. Apakah client bisa upload ulang bukti jika salah?**
> Rencananya: selama status masih `pending_verification`, client bisa upload ulang menimpa foto sebelumnya.

---

## Catatan Teknis

- **File Upload**: `Storage::putFileAs()` ke disk `public`, folder `transfer-proofs/{payment_id}/`
- **Keamanan**: validasi tipe file (`jpeg,jpg,png,webp`) dan ukuran maks 5MB di server-side
- **Idempotency**: cek existing payment `pending`/`pending_verification` untuk `invitation_id` yang sama sebelum create baru
- **Auth Guard**: semua route `/admin/manual-transfer/*` harus dilindungi middleware `auth` + role `admin`

# Implementation Plan: Fitur Saldo (Balance Top-Up System)

## Ringkasan

Mengubah alur pembayaran dari **direct payment gateway → beli undangan/paket** menjadi **top-up saldo via payment gateway → bayar undangan/paket dengan saldo**.

### Kondisi Saat Ini (BEFORE)
```
Client pilih paket/undangan → Checkout → Payment Gateway (Xendit/Tripay) → Callback → Aktivasi
```

### Kondisi Setelah Implementasi (AFTER)
```
Client top-up saldo → Payment Gateway (Xendit/Tripay) → Callback → Saldo bertambah
Client pilih paket/undangan → Bayar dengan saldo → Saldo terpotong → Aktivasi
```

### Alur Admin
```
Admin → Halaman Manajemen Saldo → Lihat semua saldo user → Tambah/Kurangi saldo manual → Lihat riwayat transaksi saldo
```

---

## Temuan Kondisi Saat Ini

### Database
- ✅ Kolom `balance` sudah ada di tabel `users` (migrasi `2026_04_18_000001_add_balance_to_users_table.php`)
- ✅ Tipe `decimal(15,2)` dengan default `0`
- ❌ Belum ada tabel `balance_transactions` untuk audit trail / riwayat mutasi saldo
- ❌ Belum ada mekanisme top-up, hanya ada kolom balance yang belum digunakan

### Payment Flow
- `CheckoutController` → langsung ke payment gateway untuk bayar undangan
- `ClientPackageController` → langsung ke payment gateway untuk bayar paket/subscription
- `PaymentOrchestratorService` → orchestrate ke Xendit/Tripay untuk semua checkout
- `PaymentCallbackController` → handle callback, langsung aktivasi subscription/invitation

### Halaman Terkait yang Akan Terpengaruh

| Halaman | File | Perubahan |
|---------|------|-----------|
| Client Dashboard | `client/dashboard/index.blade.php`, `Client\DashboardController` | Tampilkan saldo, tombol top-up |
| Client Checkout Undangan | `client/checkout/show.blade.php`, `Client\CheckoutController` | Ubah dari gateway → bayar saldo |
| Client Checkout Status | `client/checkout/status.blade.php` | Update status flow |
| Client Package Checkout | `client/packages/checkout.blade.php`, `Client\ClientPackageController` | Ubah dari gateway → bayar saldo |
| Client Package Checkout Status | `client/packages/checkout-status.blade.php` | Update status flow |
| Client Package Select | `client/packages/select.blade.php` | Tampilkan info saldo |
| Admin Payments | `admin/payments/index.blade.php`, `Admin\PaymentController` | Filter tipe top-up vs pembelian |
| Admin Users | `admin/users/index.blade.php`, `Admin\UserController` | Tampilkan kolom saldo |
| Admin User Edit | `admin/users/edit.blade.php` | Tidak diubah (saldo dikelola via halaman terpisah) |
| Payment Callback | `PaymentCallbackController` | Ubah flow: callback → tambah saldo |
| Payment Orchestrator | `PaymentOrchestratorService` | Refactor untuk top-up only |

---

## Proposed Changes

### 1. Database — Migration Baru

#### [NEW] `database/migrations/2026_05_26_000001_create_balance_transactions_table.php`

Tabel `balance_transactions` untuk mencatat setiap mutasi saldo (audit trail lengkap).

```php
Schema::create('balance_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['topup', 'purchase', 'refund', 'adjustment']);
    $table->decimal('amount', 15, 2);            // Jumlah mutasi (positif = masuk, negatif = keluar)
    $table->decimal('balance_before', 15, 2);     // Saldo sebelum transaksi
    $table->decimal('balance_after', 15, 2);      // Saldo setelah transaksi
    $table->string('description')->nullable();     // Deskripsi transaksi
    $table->string('reference_type')->nullable();  // 'payment', 'invitation', 'subscription', 'manual'
    $table->unsignedBigInteger('reference_id')->nullable(); // ID referensi terkait
    $table->foreignId('performed_by')->nullable()->constrained('users'); // Admin yang melakukan (untuk adjustment)
    $table->text('admin_note')->nullable();        // Catatan admin (untuk adjustment)
    $table->timestamps();

    $table->index(['user_id', 'type']);
    $table->index(['user_id', 'created_at']);
    $table->index(['reference_type', 'reference_id']);
});
```

**Penjelasan tipe transaksi:**
- `topup` → Saldo bertambah dari payment gateway (positif)
- `purchase` → Saldo terpotong untuk bayar undangan/paket (negatif)
- `refund` → Pengembalian saldo (positif)
- `adjustment` → Penyesuaian manual oleh admin (positif atau negatif)

#### [MODIFY] Tabel `payments` — Tambah kolom `payment_purpose`

```php
Schema::table('payments', function (Blueprint $table) {
    $table->enum('payment_purpose', ['topup', 'invitation', 'subscription'])
          ->default('topup')
          ->after('payment_status');
});
```

Kolom ini untuk membedakan payment yang dibuat untuk top-up saldo vs pembelian langsung (legacy data).

---

### 2. Model Layer

#### [NEW] `app/Models/BalanceTransaction.php`

```php
class BalanceTransaction extends Model
{
    const TYPE_TOPUP = 'topup';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_REFUND = 'refund';
    const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'user_id', 'type', 'amount', 'balance_before', 'balance_after',
        'description', 'reference_type', 'reference_id',
        'performed_by', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    // Relations: user(), performedBy()
}
```

#### [MODIFY] `app/Models/User.php`

Tambah relasi dan helper method saldo:

```php
// Relasi baru
public function balanceTransactions(): HasMany
{
    return $this->hasMany(BalanceTransaction::class);
}

// Helper methods
public function hasSufficientBalance(float $amount): bool
{
    return (float) $this->balance >= $amount;
}

public function addBalance(float $amount): void
{
    $this->increment('balance', $amount);
}

public function deductBalance(float $amount): void
{
    if (!$this->hasSufficientBalance($amount)) {
        throw new \RuntimeException('Saldo tidak mencukupi.');
    }
    $this->decrement('balance', $amount);
}
```

#### [MODIFY] `app/Models/Payment.php`

Tambah `payment_purpose` ke `$fillable` dan constant baru:

```php
public const PURPOSE_TOPUP = 'topup';
public const PURPOSE_INVITATION = 'invitation';
public const PURPOSE_SUBSCRIPTION = 'subscription';
```

---

### 3. Service Layer

#### [NEW] `app/Services/BalanceService.php`

Service utama untuk semua operasi saldo. **Semua mutasi saldo HARUS melalui service ini** untuk menjamin konsistensi dan audit trail.

```php
class BalanceService
{
    /**
     * Top-up saldo dari payment gateway callback
     */
    public function topUp(User $user, float $amount, Payment $payment): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $payment) {
            $user->lockForUpdate(); // Pessimistic lock
            $balanceBefore = (float) $user->balance;
            $user->addBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $user->id,
                'type'           => BalanceTransaction::TYPE_TOPUP,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => "Top-up saldo via {$payment->payment_gateway}",
                'reference_type' => 'payment',
                'reference_id'   => $payment->id,
            ]);
        });
    }

    /**
     * Potong saldo untuk pembelian undangan
     */
    public function purchaseInvitation(User $user, float $amount, Invitation $invitation): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $invitation) {
            $user->lockForUpdate();
            $balanceBefore = (float) $user->balance;
            $user->deductBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $user->id,
                'type'           => BalanceTransaction::TYPE_PURCHASE,
                'amount'         => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $amount,
                'description'    => "Pembelian undangan: {$invitation->title}",
                'reference_type' => 'invitation',
                'reference_id'   => $invitation->id,
            ]);
        });
    }

    /**
     * Potong saldo untuk pembelian paket subscription
     */
    public function purchaseSubscription(User $user, float $amount, ClientPackageSubscription $sub): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $sub) {
            $user->lockForUpdate();
            $balanceBefore = (float) $user->balance;
            $user->deductBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $user->id,
                'type'           => BalanceTransaction::TYPE_PURCHASE,
                'amount'         => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $amount,
                'description'    => "Langganan paket: {$sub->package->name}",
                'reference_type' => 'subscription',
                'reference_id'   => $sub->id,
            ]);
        });
    }

    /**
     * Adjustment saldo oleh admin (bisa tambah atau kurangi)
     */
    public function adminAdjustment(
        User $user,
        float $amount,       // Positif = tambah, Negatif = kurangi
        User $admin,
        string $note = ''
    ): BalanceTransaction {
        return DB::transaction(function () use ($user, $amount, $admin, $note) {
            $user->lockForUpdate();
            $balanceBefore = (float) $user->balance;

            if ($amount > 0) {
                $user->addBalance($amount);
            } else {
                $user->deductBalance(abs($amount));
            }

            return BalanceTransaction::create([
                'user_id'        => $user->id,
                'type'           => BalanceTransaction::TYPE_ADJUSTMENT,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => $amount > 0 ? 'Penambahan saldo oleh admin' : 'Pengurangan saldo oleh admin',
                'reference_type' => 'manual',
                'reference_id'   => null,
                'performed_by'   => $admin->id,
                'admin_note'     => $note,
            ]);
        });
    }

    /**
     * Refund saldo
     */
    public function refund(User $user, float $amount, string $reason, ?int $referenceId = null): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $reason, $referenceId) {
            $user->lockForUpdate();
            $balanceBefore = (float) $user->balance;
            $user->addBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $user->id,
                'type'           => BalanceTransaction::TYPE_REFUND,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => "Refund: {$reason}",
                'reference_type' => 'refund',
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Ambil riwayat transaksi saldo user
     */
    public function getTransactionHistory(int $userId, int $perPage = 20)
    {
        return BalanceTransaction::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
```

#### [MODIFY] `app/Services/Payments/PaymentOrchestratorService.php`

**Perubahan besar:** Refactor `createCheckoutPayment()` menjadi `createTopUpPayment()`.

- Hapus logika yang terikat ke invitation/subscription dari payment gateway
- Payment gateway sekarang hanya membuat transaksi top-up
- Method baru: `createTopUpPayment(User $user, int $amount, array $validated): array`
- Pertahankan `calculateBilling()` tapi adaptasi untuk billing top-up (tanpa coupon/referral di top-up)
- Pertahankan `availableGateways()`, `channelMap()`, `isDevModeEnabled()` tanpa perubahan

```php
public function createTopUpPayment(User $user, int $amount, array $validated): array
{
    // Validasi minimum top-up
    // Build order ID: TOPUP-{userId}-{timestamp}
    // Create Payment record dengan payment_purpose = 'topup'
    // Dispatch ke gateway (Xendit/Tripay)
    // Return result
}
```

#### [MODIFY] `app/Http/Controllers/PaymentCallbackController.php`

**Perubahan logika callback setelah payment `PAID`:**

```
SEBELUM: callback → markAsPaid → activateSubscription / markInvitationPaid
SESUDAH: callback → markAsPaid → topUp saldo user via BalanceService
```

- Hapus call ke `activateSubscriptionIfNeeded()` dan `markInvitationAsPaidAwaitingReview()` dari flow callback
- Tambah call ke `BalanceService::topUp()` setelah payment marked as paid
- Pertahankan semua validasi webhook yang sudah ada (idempotency, amount check, dll)

---

### 4. Controller Layer — Client Side

#### [NEW] `app/Http/Controllers/Client/BalanceController.php`

Controller baru untuk halaman saldo client:

```php
class BalanceController extends Controller
{
    // GET /client/balance → Halaman saldo + riwayat transaksi
    public function index();

    // GET /client/balance/topup → Halaman top-up (pilih nominal + gateway)
    public function topupForm();

    // POST /client/balance/topup → Proses top-up ke payment gateway
    public function topupProcess(Request $request);

    // GET /client/balance/topup/status → Status pembayaran top-up
    public function topupStatus();
}
```

#### [MODIFY] `app/Http/Controllers/Client/CheckoutController.php`

**Perubahan fundamental:** Checkout undangan sekarang menggunakan saldo.

```
SEBELUM: show() → tampilkan gateway → process() → kirim ke gateway
SESUDAH: show() → tampilkan ringkasan + saldo → process() → potong saldo → aktivasi
```

- `show()`: Tampilkan ringkasan order + info saldo user + apakah cukup
- `process()`: Validasi saldo cukup → potong saldo via `BalanceService` → redirect ke status
- Hapus semua logika gateway (gateway, payment_type, channel)
- Pertahankan logika coupon & referral (diskon tetap berlaku)
- Hapus `simulatePaid()` (tidak diperlukan lagi, pembayaran instan dengan saldo)

#### [MODIFY] `app/Http/Controllers/Client\ClientPackageController.php`

**Perubahan sama seperti CheckoutController:**

- `checkoutShow()`: Tampilkan ringkasan + saldo
- `checkoutProcess()`: Potong saldo → aktivasi subscription
- Hapus semua logika gateway dari checkout paket
- Hapus `checkoutSimulatePaid()`

#### [MODIFY] `app/Http/Controllers/Client/DashboardController.php`

- Tambah data `balance` ke view
- Tampilkan saldo di dashboard client

---

### 5. Controller Layer — Admin Side

#### [NEW] `app/Http/Controllers/Admin/BalanceController.php`

Controller baru untuk manajemen saldo di admin:

```php
class BalanceController extends Controller
{
    // GET /admin/balance → Daftar semua user + saldo + search/filter
    public function index(Request $request);

    // GET /admin/balance/{user} → Detail saldo + riwayat transaksi user tertentu
    public function show(User $user);

    // POST /admin/balance/{user}/adjust → Tambah/kurangi saldo manual
    public function adjust(Request $request, User $user);

    // GET /admin/balance/transactions → Semua transaksi saldo (global)
    public function transactions(Request $request);
}
```

**Fitur halaman admin saldo:**
1. **Daftar User dengan Saldo** — Tabel semua user client beserta saldo, search by nama/email, sort by saldo
2. **Detail Saldo User** — Riwayat mutasi lengkap (topup, pembelian, adjustment), saldo saat ini
3. **Adjustment Form** — Form untuk tambah/kurangi saldo dengan alasan wajib diisi
4. **Log Transaksi Global** — Semua transaksi saldo dari semua user, filter by tipe/tanggal

#### [MODIFY] `app/Http/Controllers/Admin/PaymentController.php`

- Tambah filter `payment_purpose` (topup vs legacy)
- Tambah statistik top-up di dashboard payment

---

### 6. View Layer — Client Side

#### [NEW] `resources/views/client/balance/index.blade.php`

Halaman utama saldo client:
- Card besar menampilkan saldo saat ini (angka besar, prominent)
- Tombol "Top Up Saldo" yang menonjol
- Tabel riwayat transaksi saldo dengan pagination
- Badge warna per tipe: hijau (topup), merah (purchase), biru (adjustment), kuning (refund)
- Filter by tipe transaksi

#### [NEW] `resources/views/client/balance/topup.blade.php`

Halaman top-up saldo:
- Pilihan nominal preset (Rp25.000, Rp50.000, Rp100.000, Rp200.000, Rp500.000)
- Input nominal custom
- Pilih gateway + metode pembayaran (reuse dari checkout lama)
- Ringkasan: nominal top-up + fee (jika ada) = total bayar
- Tombol "Bayar & Top Up Saldo"

#### [NEW] `resources/views/client/balance/topup-status.blade.php`

Halaman status pembayaran top-up:
- Status pembayaran (pending/paid/expired/failed)
- Jika paid: "Saldo berhasil ditambahkan!" + saldo terbaru
- Jika pending: countdown expiry + tombol lanjutkan pembayaran
- Tombol kembali ke halaman saldo

#### [MODIFY] `resources/views/client/checkout/show.blade.php`

**Perubahan total UI checkout undangan:**

```
SEBELUM: Form pilih gateway + channel + coupon → Bayar via gateway
SESUDAH: Ringkasan order + info saldo → Bayar dengan saldo (1 klik)
```

- Tampilkan saldo saat ini
- Jika saldo cukup: tombol "Bayar dengan Saldo" (warna hijau, menonjol)
- Jika saldo kurang: tampilkan kekurangan + tombol "Top Up Saldo Dulu" yang redirect ke halaman top-up
- Pertahankan coupon field (diskon tetap berlaku)
- Hapus semua UI gateway/channel/payment_type

#### [MODIFY] `resources/views/client/checkout/status.blade.php`

- Sesuaikan untuk menampilkan status pembayaran saldo (bukan gateway)
- Tampilkan sisa saldo setelah pembelian

#### [MODIFY] `resources/views/client/packages/checkout.blade.php`

**Perubahan sama seperti checkout undangan:**

- Tampilkan saldo + tombol bayar saldo
- Jika saldo kurang: redirect ke top-up

#### [MODIFY] `resources/views/client/packages/checkout-status.blade.php`

- Sesuaikan status untuk pembayaran saldo

#### [MODIFY] `resources/views/client/packages/select.blade.php`

- Tampilkan saldo user di atas daftar paket
- Indikator visual paket mana yang bisa dibeli dengan saldo saat ini

#### [MODIFY] `resources/views/client/dashboard/index.blade.php`

- Tambah card saldo di bagian atas dashboard (sebelum stats)
- Tampilkan saldo, tombol top-up, dan link ke riwayat saldo

---

### 7. View Layer — Admin Side

#### [NEW] `resources/views/admin/balance/index.blade.php`

Halaman utama manajemen saldo admin:
- **Stats cards**: Total saldo semua user, Total top-up bulan ini, Total pembelian bulan ini, Total adjustment bulan ini
- **Tabel user**: Kolom nama, email, saldo, total top-up, total pembelian, aksi (Detail / Adjust)
- Search by nama/email
- Sort by saldo (tertinggi/terendah)
- Export (opsional, fase 2)

#### [NEW] `resources/views/admin/balance/show.blade.php`

Halaman detail saldo per user:
- Info user (nama, email, role)
- Card saldo saat ini (angka besar)
- **Form adjustment**: Pilih tambah/kurangi → masukkan nominal → wajib isi alasan → tombol Simpan
- Tabel riwayat transaksi saldo user ini dengan pagination
- Badge warna per tipe transaksi

#### [NEW] `resources/views/admin/balance/transactions.blade.php`

Halaman log transaksi global:
- Tabel semua transaksi saldo dari semua user
- Filter by: tipe (topup/purchase/refund/adjustment), range tanggal, user
- Kolom: User, Tipe, Jumlah, Saldo Before → After, Deskripsi, Waktu, Performed By

#### [MODIFY] `resources/views/admin/users/index.blade.php`

- Tambah kolom "Saldo" di tabel user
- Link ke halaman detail saldo

---

### 8. Routes

#### [MODIFY] `routes/client.php`

Tambah route group baru:

```php
// Balance & Top-up
Route::get('/balance', [BalanceController::class, 'index'])->name('balance.index');
Route::get('/balance/topup', [BalanceController::class, 'topupForm'])->name('balance.topup');
Route::post('/balance/topup', [BalanceController::class, 'topupProcess'])->name('balance.topup.process');
Route::get('/balance/topup/status', [BalanceController::class, 'topupStatus'])->name('balance.topup.status');
```

#### [MODIFY] `routes/admin.php`

Tambah route group baru:

```php
// Balance Management
Route::get('/balance', [BalanceController::class, 'index'])->name('balance.index');
Route::get('/balance/transactions', [BalanceController::class, 'transactions'])->name('balance.transactions');
Route::get('/balance/{user}', [BalanceController::class, 'show'])->name('balance.show');
Route::post('/balance/{user}/adjust', [BalanceController::class, 'adjust'])->name('balance.adjust');
```

---

### 9. Navigasi / Sidebar

#### [MODIFY] Layout Client (sidebar/navbar)

- Tambah menu "Saldo" atau "Dompet" dengan icon `fa-wallet`
- Tampilkan badge saldo di sidebar/navbar

#### [MODIFY] Layout Admin (sidebar)

- Tambah menu "Manajemen Saldo" dengan icon `fa-wallet` di bawah menu "Payments"
- Sub-menu: Saldo User, Log Transaksi

---

## Diagram Alur Lengkap

### Alur Top-Up Saldo (Client)

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ Halaman Saldo│───▶│ Halaman     │───▶│ Payment     │───▶│ Callback     │
│ (lihat saldo)│    │ Top-Up       │    │ Gateway      │    │ (Xendit/     │
│              │    │ (pilih       │    │ (bayar)      │    │  Tripay)     │
│              │    │  nominal +   │    │              │    │              │
│              │    │  gateway)    │    │              │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────┬───────┘
                                                                   │
                                                                   ▼
                                                           ┌──────────────┐
                                                           │ BalanceService│
                                                           │ .topUp()     │
                                                           │ → saldo +   │
                                                           │ → log txn   │
                                                           └──────────────┘
```

### Alur Pembelian Undangan/Paket (Client)

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ Pilih Paket/ │───▶│ Checkout     │───▶│ Cek saldo    │───▶│ BalanceService│
│ Buat Undangan│    │ (ringkasan + │    │ cukup?       │    │ .purchase()  │
│              │    │  info saldo) │    │              │    │ → saldo -    │
│              │    │              │    │ ✅ Ya ───────│───▶│ → aktivasi   │
│              │    │              │    │ ❌ Tidak ────│──▶ │ → redirect   │
│              │    │              │    │   → top up   │    │   ke top-up  │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

### Alur Admin Adjustment

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ Admin:       │───▶│ Detail Saldo │───▶│ BalanceService│
│ Daftar User  │    │ User         │    │ .adminAdjust()│
│ + Saldo      │    │ (form adjust │    │ → saldo ±    │
│              │    │  + riwayat)  │    │ → log txn    │
│              │    │              │    │ → audit      │
└──────────────┘    └──────────────┘    └──────────────┘
```

---

## Urutan Implementasi (Tahapan)

### Fase 1: Foundation (Database + Model + Service)
1. Buat migration `balance_transactions`
2. Buat migration alter `payments` (tambah `payment_purpose`)
3. Buat model `BalanceTransaction`
4. Modifikasi model `User` (tambah helper + relasi)
5. Modifikasi model `Payment` (tambah `payment_purpose`)
6. Buat `BalanceService` dengan semua method
7. Jalankan migration

### Fase 2: Top-Up Flow (Client)
1. Refactor `PaymentOrchestratorService` → tambah `createTopUpPayment()`
2. Buat `Client\BalanceController`
3. Buat views: `client/balance/index`, `client/balance/topup`, `client/balance/topup-status`
4. Tambah routes client untuk balance
5. Modifikasi `PaymentCallbackController` → callback top-up menambah saldo
6. Update navigasi/sidebar client

### Fase 3: Checkout dengan Saldo (Client)
1. Refactor `Client\CheckoutController` → bayar dengan saldo
2. Refactor `Client\ClientPackageController` → bayar subscription dengan saldo
3. Update views checkout undangan (`client/checkout/show`, `client/checkout/status`)
4. Update views checkout paket (`client/packages/checkout`, `client/packages/checkout-status`, `client/packages/select`)
5. Update `Client\DashboardController` → tampilkan saldo
6. Update view dashboard client

### Fase 4: Admin Panel
1. Buat `Admin\BalanceController`
2. Buat views: `admin/balance/index`, `admin/balance/show`, `admin/balance/transactions`
3. Tambah routes admin untuk balance
4. Update `admin/users/index.blade.php` → tambah kolom saldo
5. Update navigasi/sidebar admin
6. Update `Admin\PaymentController` → filter tipe payment

### Fase 5: Polish & Testing
1. Audit log untuk setiap adjustment admin
2. Notifikasi Telegram saat top-up berhasil / saldo rendah
3. Testing end-to-end semua flow

---

## File Inventory Lengkap

### File Baru (10 file)
| # | File | Tipe |
|---|------|------|
| 1 | `database/migrations/2026_05_26_000001_create_balance_transactions_table.php` | Migration |
| 2 | `database/migrations/2026_05_26_000002_add_payment_purpose_to_payments_table.php` | Migration |
| 3 | `app/Models/BalanceTransaction.php` | Model |
| 4 | `app/Services/BalanceService.php` | Service |
| 5 | `app/Http/Controllers/Client/BalanceController.php` | Controller |
| 6 | `app/Http/Controllers/Admin/BalanceController.php` | Controller |
| 7 | `resources/views/client/balance/index.blade.php` | View |
| 8 | `resources/views/client/balance/topup.blade.php` | View |
| 9 | `resources/views/client/balance/topup-status.blade.php` | View |
| 10 | `resources/views/admin/balance/index.blade.php` | View |
| 11 | `resources/views/admin/balance/show.blade.php` | View |
| 12 | `resources/views/admin/balance/transactions.blade.php` | View |

### File yang Dimodifikasi (16 file)
| # | File | Perubahan |
|---|------|-----------|
| 1 | `app/Models/User.php` | Tambah relasi + helper saldo |
| 2 | `app/Models/Payment.php` | Tambah `payment_purpose` |
| 3 | `app/Services/Payments/PaymentOrchestratorService.php` | Tambah `createTopUpPayment()` |
| 4 | `app/Http/Controllers/PaymentCallbackController.php` | Callback → top-up saldo |
| 5 | `app/Http/Controllers/Client/CheckoutController.php` | Bayar dengan saldo |
| 6 | `app/Http/Controllers/Client/ClientPackageController.php` | Bayar subscription dengan saldo |
| 7 | `app/Http/Controllers/Client/DashboardController.php` | Tampilkan saldo |
| 8 | `app/Http/Controllers/Admin/PaymentController.php` | Filter payment purpose |
| 9 | `routes/client.php` | Tambah route balance |
| 10 | `routes/admin.php` | Tambah route balance admin |
| 11 | `resources/views/client/checkout/show.blade.php` | UI bayar dengan saldo |
| 12 | `resources/views/client/checkout/status.blade.php` | Status saldo |
| 13 | `resources/views/client/packages/checkout.blade.php` | UI bayar dengan saldo |
| 14 | `resources/views/client/packages/checkout-status.blade.php` | Status saldo |
| 15 | `resources/views/client/packages/select.blade.php` | Tampilkan saldo |
| 16 | `resources/views/client/dashboard/index.blade.php` | Card saldo |
| 17 | `resources/views/admin/users/index.blade.php` | Kolom saldo |

---

## Keamanan & Konsistensi

1. **Pessimistic Locking**: Semua mutasi saldo menggunakan `lockForUpdate()` untuk mencegah race condition
2. **Database Transaction**: Setiap operasi saldo dibungkus dalam `DB::transaction()`
3. **Audit Trail**: Setiap mutasi tercatat di `balance_transactions` dengan before/after balance
4. **Admin Accountability**: Setiap adjustment admin tercatat siapa yang melakukan (`performed_by`)
5. **Validasi Saldo**: Pengecekan saldo dilakukan di service layer, bukan hanya di controller
6. **Non-negative Balance**: Saldo tidak boleh negatif (validasi di `deductBalance()`)

---

## Verification Plan

### Automated Testing
```bash
php artisan migrate                    # Jalankan migration baru
php artisan tinker                     # Test manual BalanceService
```

### Manual Testing Scenarios

| # | Skenario | Expected Result |
|---|----------|-----------------|
| 1 | Client top-up Rp100.000 via Xendit | Saldo bertambah Rp100.000, record di balance_transactions |
| 2 | Client bayar undangan Rp50.000 (saldo cukup) | Saldo berkurang Rp50.000, undangan ter-checkout |
| 3 | Client bayar undangan Rp50.000 (saldo kurang) | Muncul pesan "Saldo kurang", redirect ke top-up |
| 4 | Client bayar paket Rp75.000 dengan saldo | Saldo berkurang, subscription aktif |
| 5 | Admin tambah saldo Rp200.000 ke user X | Saldo user X bertambah, tercatat di log |
| 6 | Admin kurangi saldo Rp50.000 dari user X | Saldo berkurang, tercatat siapa admin-nya |
| 7 | Admin lihat riwayat transaksi user X | Tampil semua mutasi dengan balance before/after |
| 8 | Callback Xendit paid untuk top-up | Saldo user bertambah sesuai nominal |
| 9 | Callback duplicate top-up | Saldo tidak bertambah dua kali (idempotency) |
| 10 | Client lihat dashboard | Saldo tampil di card atas |

---

## Asumsi

1. Saldo dalam mata uang Rupiah (IDR), format `decimal(15,2)`
2. Saldo minimum adalah 0 (tidak boleh negatif)
3. Tidak ada biaya admin/fee untuk top-up saldo (bisa ditambah nanti via setting)
4. Coupon tetap berlaku untuk checkout undangan/paket (mengurangi harga sebelum potong saldo)
5. Referral tetap diproses saat checkout undangan/paket (commission dihitung dari harga sebelum diskon)
6. Data payment lama (yang sudah menggunakan gateway langsung) tetap utuh dan compatible
7. Fitur top-up menggunakan gateway yang sama (Xendit sebagai primary, Tripay sebagai fallback)
8. Nominal top-up minimum akan diatur via Settings admin

# Payment Production Implementation Plan

## Summary
Simpan dokumen implementasi nanti sebagai `PAYMENT_PRODUCTION_IMPLEMENTATION_PLAN.md` di root folder `Invitation/`.

Rekomendasi gateway:
- Gunakan `Xendit` sebagai gateway production utama untuk fase pertama.
- Scope launch awal: `QRIS + E-Wallet`.
- `Tripay` diposisikan sebagai fase 2 atau fallback plan, bukan dual-active saat go-live awal.

Alasan rekomendasi:
- Codebase saat ini sudah punya jalur `Xendit` dan `Tripay`, tetapi arsitekturnya masih duplikatif dan belum production-grade.
- Untuk launch awal yang aman, satu gateway lebih mudah distabilkan, diuji, dimonitor, dan didukung operasional.
- Dari dokumentasi resmi, Xendit mendukung `QRIS`, `eWallet`, hosted payment page/invoice, dan webhook untuk konfirmasi pembayaran.  
  Sumber:
  - https://docs.xendit.co/docs/qris
  - https://docs.xendit.co/id/ewallet
  - https://docs.xendit.co/docs/how-payment-links-work
- Tripay tetap layak, tetapi untuk project ini lebih tepat dijadikan fase lanjutan setelah core billing flow stabil.

Temuan kondisi saat ini:
- Checkout invitation dan checkout subscription hampir duplikat.
- Gateway dipanggil langsung dari controller, belum ada abstraction layer tunggal.
- Mode development terlalu mudah jatuh ke mock flow.
- Callback sudah ada dan sudah punya idempotency receipt, ini pondasi yang baik.
- Admin config ada, tetapi belum cukup untuk readiness production, observability, dan operasi support.
- Payment status model masih sederhana: `pending/paid/failed`, belum lengkap untuk lifecycle production.

## Key Changes
### 1. Arsitektur pembayaran
- Bentuk satu modul payment terpusat untuk semua flow: invitation checkout dan package subscription checkout.
- Pindahkan seluruh logika create payment, billing, coupon, referral, tax, expiry, dan gateway dispatch dari controller ke service orchestration tunggal.
- Definisikan interface gateway tunggal, misalnya:
  - `createPaymentIntent(...)`
  - `verifyWebhook(...)`
  - `parseWebhook(...)`
  - `queryPaymentStatus(...)`
- Implementasi pertama hanya `XenditGateway`.
- Pertahankan `TripayGateway` sebagai adapter terpisah tetapi nonaktif untuk production v1.

### 2. Model data dan lifecycle transaksi
- Jadikan `Payment` sebagai source of truth untuk semua pembayaran.
- Tambahkan lifecycle status yang lebih production-ready:
  - `draft`
  - `pending`
  - `paid`
  - `expired`
  - `failed`
  - `cancelled`
- Pisahkan identifier internal vs gateway:
  - internal order/payment number
  - external gateway reference
- Tegaskan invariant:
  - satu payment record hanya boleh punya satu lifecycle final
  - callback duplicate tidak boleh mengubah payment yang sudah final
  - amount mismatch harus ditolak dan dicatat
- Pastikan payment untuk invitation dan subscription memakai engine yang sama, hanya beda `payable target`.

### 3. Checkout flow production
- Invitation checkout dan subscription checkout harus memakai shared payment service yang sama.
- Hapus ketergantungan decision logic gateway dari controller.
- Batasi scope payment method production awal ke:
  - `QRIS`
  - `OVO`
  - `DANA`
  - `ShopeePay`
  - `LinkAja` hanya jika benar-benar aktif di akun Xendit production
- Ubah UX checkout:
  - tampilkan hanya channel yang benar-benar aktif
  - satu CTA pembayaran yang jelas
  - tampilkan countdown expiry dan status transaksi terakhir
  - tombol retry membuat transaksi baru hanya jika transaksi lama expired/failed
- Nonaktifkan mock payment di production secara keras, bukan hanya bergantung env lokal.

### 4. Webhook, reconciliation, dan reliability
- Pertahankan callback controller tetapi pindahkan parsing bisnis ke service.
- Wajibkan verifikasi webhook:
  - token/signature valid
  - amount valid
  - gateway reference cocok
  - payment belum final
- Simpan callback mentah dan hasil parsing untuk audit.
- Tambahkan reconciliation job terjadwal:
  - cek payment `pending` yang mendekati expiry
  - cek payment `pending` yang tidak pernah menerima webhook
  - sinkronkan status dari gateway bila perlu
- Tambahkan admin observability minimum:
  - daftar callback terbaru
  - payment mismatch/error log
  - retry reconciliation manual
  - indikator gateway config sehat/tidak

### 5. Admin config dan operational readiness
- Rapikan admin payment settings menjadi production-safe:
  - gateway active flag
  - environment mode
  - API keys / callback secret
  - enabled channels
  - expiry duration
  - tax setting
  - promo/coupon behavior
- Tambahkan validation config:
  - tidak bisa enable gateway bila credential inti kosong
  - tidak bisa enable production mode bila callback secret belum diisi
- Tambahkan checklist operasional di plan file:
  - set webhook URL production
  - whitelist domain/callback URL
  - test transaction sandbox
  - test paid callback
  - test expired callback
  - test amount mismatch
  - test duplicate callback

## Important Interfaces
- Root plan file yang nanti dibuat: `Invitation/PAYMENT_PRODUCTION_IMPLEMENTATION_PLAN.md`
- Abstraksi baru yang perlu dirancang:
  - `PaymentGatewayInterface`
  - `PaymentOrchestratorService`
  - `PaymentStatusSyncService`
- Shared payable flow:
  - invitation payment
  - client package subscription payment
- Admin/system endpoints tambahan yang sebaiknya ada:
  - manual reconciliation trigger
  - callback audit viewer
  - gateway health check
- Public callback contract tetap:
  - `POST /callback/xendit`
- Callback `Tripay` tetap dipertahankan di codebase, tetapi tidak dijadikan production primary untuk fase pertama.

## Test Plan
- Invitation checkout dengan Xendit:
  - create payment sukses
  - redirect/payment URL tersimpan
  - callback `paid` mengubah status menjadi `paid`
  - undangan tidak otomatis aktif bila flow bisnis masih butuh review admin
- Subscription checkout dengan Xendit:
  - create payment sukses
  - callback `paid` mengaktifkan subscription
- Expiry flow:
  - payment pending melewati expiry menjadi `expired`
  - user dapat membuat transaksi baru
- Duplicate callback:
  - callback kedua tidak mengubah data final
- Security:
  - callback token/signature salah ditolak
  - amount mismatch ditolak
  - payment reference tak dikenal ditolak
- Operational:
  - reconciliation job bisa menyelesaikan payment pending yang missed webhook
  - admin dapat melihat log callback dan error status

## Assumptions
- Launch awal hanya untuk Indonesia.
- Scope payment phase 1 hanya `QRIS + E-Wallet`.
- Go-live memakai satu gateway aktif: `Xendit`.
- `Tripay` tidak dihapus, hanya diturunkan menjadi fase berikutnya/fallback candidate.
- Invitation tetap mengikuti flow bisnis sekarang: `paid` tidak otomatis berarti `approved/active`.
- Saat keluar dari Plan Mode, implementer membuat file root `PAYMENT_PRODUCTION_IMPLEMENTATION_PLAN.md` berdasarkan plan ini, lalu mengeksekusi refactor bertahap mulai dari shared payment orchestration dan Xendit-first rollout.

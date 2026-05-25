# Payment Production Implementation Plan

## Summary
- Gateway production utama fase pertama: `Xendit`
- Scope launch awal: `QRIS + E-Wallet`
- `Tripay` dipertahankan sebagai adapter fase 2 / fallback candidate
- Payment invitation dan subscription sekarang diarahkan ke orchestration service yang sama

## Architecture
- `PaymentGatewayInterface` menjadi kontrak gateway tunggal
- `PaymentOrchestratorService` menangani:
  - gateway availability
  - channel mapping
  - billing calculation
  - coupon validation
  - referral resolution
  - payment creation
  - gateway dispatch
- `PaymentStatusSyncService` menangani:
  - sync pending payment dari gateway
  - callback observability
  - reconciliation support

## Production Decisions
- Launch awal memakai satu gateway aktif untuk menekan kompleksitas operasional
- Payment method production v1 dibatasi ke:
  - QRIS
  - OVO
  - DANA
  - ShopeePay
  - LinkAja bila aktif di akun gateway
- Callback public tetap:
  - `POST /callback/xendit`
  - `POST /callback/tripay`
- Invitation yang dibayar tetap mengikuti flow review admin saat ini

## Admin Readiness
- Konfigurasi admin harus memvalidasi:
  - gateway aktif tidak boleh tanpa credential inti
  - Xendit production wajib punya callback token
  - expiry checkout harus berada dalam rentang aman
- Halaman admin payment perlu menyediakan:
  - manual sync pending payments
  - daftar callback terbaru
  - status reconciliation terakhir

## Operational Checklist
1. Isi credential Xendit sandbox lalu test koneksi.
2. Jalankan transaksi sandbox invitation.
3. Jalankan transaksi sandbox subscription.
4. Verifikasi callback `paid`.
5. Verifikasi callback `expired`.
6. Verifikasi duplicate callback tidak menggandakan hasil.
7. Switch ke credential production.
8. Set callback URL production di dashboard gateway.
9. Monitor admin payment dashboard dan reconciliation hasil harian.

## Test Scenarios
- Invitation checkout membuat payment pending dan menyimpan payment URL
- Subscription checkout membuat payment pending dan callback `paid` mengaktifkan subscription
- Amount mismatch callback ditolak
- Callback invalid signature / token ditolak
- Payment pending yang melewati expiry ditandai `expired`
- Manual sync admin menyinkronkan payment pending Xendit yang missed webhook

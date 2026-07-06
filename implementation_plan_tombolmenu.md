# Rencana Implementasi: Quick-Edit Menu Button Panel (Modal-Based Editing)

## Ringkasan

Saat ini, client harus membuka halaman `/invitations/{id}/edit` yang panjang, lalu scroll untuk menemukan bagian yang ingin diubah. Ini tidak nyaman terutama di mobile. 

Ide kamu sangat solid: mengganti alur "buka halaman edit → isi form → submit" menjadi **kumpulan tombol aksi cepat** yang membuka **modal/drawer** per-section, langsung dari halaman `show` (detail undangan). Client tidak perlu pindah halaman sama sekali.

---

## Penilaian Ide

> [!NOTE]
> Ide ini sangat tepat dan mengikuti pola UX modern (seperti Notion, Canva, dan platform SaaS lainnya). Beberapa keunggulan:
> - **Zero navigation overhead** — semua edit dari satu halaman
> - **Context tetap terjaga** — client bisa sambil melihat statistik/RSVP tanpa kehilangan konteks
> - **Mobile-first** — tombol di atas saat mobile jauh lebih ergonomis
> - **Incremental save** — tiap section disimpan terpisah via PATCH/POST, tidak perlu re-submit seluruh form

---

## Analisis Sections dari `edit.blade.php`

Berdasarkan audit halaman [edit.blade.php](file:///d:/Develov/Invitation/resources/views/client/invitations/edit.blade.php), berikut 10 section yang perlu dikonversi ke tombol modal:

| # | Label Tombol | Section | Fields |
|---|---|---|---|
| 1 | 🎨 Template | sec1 | template_id |
| 2 | 🖼️ Cover & Pembuka | sec2 | title, event_type, cover_photo, opening_text |
| 3 | 💑 Data Mempelai | sec3 | groom/bride name, photo, instagram, facebook, parent names |
| 4 | ⏱️ Countdown & Waktu | sec4 | event_date, event_time |
| 5 | 🗓️ Acara & Susunan | sec5 | dynamic events (Alpine.js) |
| 6 | 📍 Lokasi | sec6 | venue_name, address, google maps, leaflet picker |
| 7 | 💌 RSVP & Ucapan | sec9 | toggle on/off RSVP, toggle ucapan |
| 8 | 💝 Tanda Kasih | sec10 | bank accounts (dynamic), gift_address |
| 9 | ✍️ Penutup | sec11 | closing_text, footer_text |
| 10 | 🎵 Musik & Live | sec12 | music_url, music_track_id, livestream settings |

---

## Desain UI/UX yang Direncanakan

### Layout di Halaman `show.blade.php`

```
[Desktop: lg:grid-cols-3]
┌─────────────────────────────┬──────────────────────────────┐
│  Main Content (col-span-2)  │  Sidebar (col-span-1)        │
│  - Informasi Acara card     │  ┌──────────────────────────┐│
│  - Admin Notes              │  │  🛠️ Edit Undangan Cepat  ││
│  - Foto                     │  │  ─────────────────────── ││
│  - Love Story               │  │  [🎨 Template]           ││
│  - IG Story                 │  │  [🖼️ Cover & Pembuka]   ││
│  - RSVP & Ucapan            │  │  [💑 Data Mempelai]      ││
│                             │  │  [⏱️ Countdown & Waktu]  ││
│                             │  │  [🗓️ Acara & Susunan]   ││
│                             │  │  [📍 Lokasi]             ││
│                             │  │  [💌 RSVP & Ucapan]      ││
│                             │  │  [💝 Tanda Kasih]        ││
│                             │  │  [✍️ Penutup]            ││
│                             │  │  [🎵 Musik & Live]       ││
│                             │  └──────────────────────────┘│
│                             │  + card Aksi lainnya         │
│                             │  + card Statistik            │
└─────────────────────────────┴──────────────────────────────┘

[Mobile: stack vertikal]
┌──────────────────────────────┐
│  🛠️ Edit Undangan Cepat     │  ← PALING ATAS di mobile
│  [🎨 Template] [🖼️ Cover]  │
│  [💑 Mempelai] [⏱️ Waktu]  │
│  [🗓️ Acara] [📍 Lokasi]    │
│  [💌 RSVP] [💝 Kasih]      │
│  [✍️ Penutup] [🎵 Musik]   │
├──────────────────────────────┤
│  Informasi Acara card        │
│  Foto, Love Story, dll       │
└──────────────────────────────┘
```

### Desain Tombol

Tombol berbentuk **grid 2 kolom** dengan ikon + label, menggunakan style premium:
- Background: `var(--bg-tertiary)` dengan hover effect
- Border: `1px solid var(--border)`
- Border-radius: `10px`
- Icon berwarna accent
- Micro-animation saat hover (translateY -2px)

### Modal / Drawer

- **Overlay** semi-transparan dengan blur backdrop
- **Card putih** muncul dari bawah (slide-up) di mobile
- **Card centered** (max-width: 600px) di desktop
- Header modal: judul section + tombol close
- Body: form fields dari section tersebut
- Footer: tombol **Simpan** + **Batal**
- Submit via form biasa atau AJAX (akan diputuskan, lihat Open Questions)

---

## Perubahan yang Direncanakan

### 1. Backend: Partial Update Routes

Saat ini semua update melalui satu route `PUT client.invitations.update`. Untuk modal, kita bisa:
- **Opsi A (Recommended):** Tetap pakai satu route `PUT`, setiap modal hanya submit field-nya sendiri (field lain tidak dikirim maka tidak diubah — perlu validasi `sometimes`)
- **Opsi B:** Buat route PATCH baru per-section (lebih clean tapi banyak route)

> [!IMPORTANT]
> Opsi A lebih cepat diimplementasi karena tidak perlu mengubah controller. Cukup pastikan validasi menggunakan `sometimes|required` bukan `required` saja agar field yang tidak disubmit tidak menyebabkan error.

#### [MODIFY] [InvitationController.php](file:///d:/Develov/Invitation/app/Http/Controllers/Client/InvitationController.php)
- Audit validasi `update()` method
- Ubah `required` jadi `sometimes|required` untuk field yang relevan agar partial update bisa bekerja

---

### 2. View: `show.blade.php` — Tambah Card Quick Edit Panel

#### [MODIFY] [show.blade.php](file:///d:/Develov/Invitation/resources/views/client/invitations/show.blade.php)

**A. Tambah Card "Edit Undangan Cepat"**

Di sidebar (`div.space-y-6`), tambahkan card baru **SEBELUM** card Aksi:

```html
<!-- Card Quick Edit -->
<div class="card p-5 order-first lg:order-none" id="quick-edit-panel">
    <h3 class="font-bold text-base mb-4">
        <i class="fas fa-sliders-h mr-2" style="color: var(--accent);"></i>
        Edit Undangan Cepat
    </h3>
    <div class="grid grid-cols-2 gap-2">
        <!-- 10 tombol -->
        <button onclick="openModal('modal-template')" class="quick-edit-btn">...</button>
        ...
    </div>
</div>
```

**B. Responsive: Mobile di Atas**

Menggunakan CSS `order` atau memindahkan card ke luar grid utama dan menampilkannya hanya di mobile di posisi atas, menggunakan kombinasi:
- Di mobile: card tampil sebelum `grid grid-cols-1 lg:grid-cols-3`
- Di desktop: card tampil di sidebar (kolom kanan) paling atas

Teknik yang dipakai: **Duplicate rendering dengan `hidden lg:block` / `block lg:hidden`** ATAU pakai CSS `order` di flex/grid.

**Rekomendasi:** Gunakan CSS `order` pada grid untuk menghindari duplikasi HTML:

```css
/* Mobile: quick-edit tampil pertama */
#quick-edit-panel { order: -1; }

/* Desktop: kembali ke posisi sidebar */
@media (min-width: 1024px) {
    #quick-edit-panel { order: 0; }
}
```

**C. Modal Templates (10 modal)**

Setiap modal berisi form dengan `action="{{ route('client.invitations.update', $invitation) }}"` dan method `PUT`, tapi hanya field dari section tersebut + `_method` spoofing.

Contoh Modal Template:
```html
<div id="modal-template" class="modal-overlay hidden" ...>
    <div class="modal-card">
        <div class="modal-header">
            <h4>🎨 Edit Template</h4>
            <button onclick="closeModal('modal-template')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="...">
                @csrf @method('PUT')
                <!-- hanya field template -->
                <select name="template_id">...</select>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</div>
```

---

### 3. CSS: Style Modal & Quick Edit Buttons

Tambahkan ke dalam `<style>` di `show.blade.php`:

```css
/* Quick Edit Buttons */
.quick-edit-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px 8px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--bg-tertiary);
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    color: var(--text);
    transition: all 0.2s ease;
    text-align: center;
}

.quick-edit-btn:hover {
    transform: translateY(-2px);
    border-color: var(--accent);
    background: var(--accent-bg);
    color: var(--accent);
    box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.15);
}

.quick-edit-btn i {
    font-size: 18px;
    color: var(--accent);
}

/* Modal Overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeIn 0.2s ease;
}

/* Modal Card */
.modal-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    border: 1px solid var(--border);
    width: 100%;
    max-width: 580px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 80px rgba(0,0,0,0.3);
    animation: slideUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-body {
    padding: 20px;
}

.modal-close-btn {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid var(--border);
    background: var(--bg-tertiary);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}

.modal-close-btn:hover {
    background: var(--danger);
    color: white;
    border-color: var(--danger);
}
```

---

### 4. JavaScript: Modal Controller

```javascript
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Tutup modal saat klik overlay (bukan card)
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
});

// Tutup dengan Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => {
            m.classList.add('hidden');
        });
        document.body.style.overflow = '';
    }
});
```

---

### 5. Khusus Modal Lokasi (sec6)

Modal Lokasi memiliki **Leaflet map picker** yang memerlukan perhatian khusus:
- Map harus di-`invalidateSize()` saat modal dibuka agar render dengan benar
- Listener map hanya diinisialisasi sekali (gunakan flag)
- Koordinat lat/lng ditulis ke input hidden di dalam form modal

```javascript
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Khusus modal lokasi
        if (id === 'modal-lokasi' && window.pickerMap) {
            setTimeout(() => window.pickerMap.invalidateSize(), 100);
        }
    }
}
```

---

### 6. Navigasi: Halaman Edit Lama

> [!IMPORTANT]
> Halaman `/invitations/{id}/edit` **tidak dihapus**, masih bisa diakses sebagai fallback. Tombol "Edit Undangan" lama di sidebar cukup diubah menjadi teks tersembunyi atau dihapus dari card Aksi, karena fungsinya sudah digantikan oleh Quick Edit Panel.

---

## Alur Submit & Flash Message

Setiap modal submit via `<form method="POST">` biasa (bukan AJAX) → setelah update, redirect kembali ke `show` dengan flash message sukses → modal sudah tertutup otomatis karena halaman refresh.

Untuk UX yang lebih seamless (tanpa full page reload), bisa ditingkatkan ke AJAX di fase 2.

---

## Rencana Urutan Implementasi

- [ ] **Fase 1** – CSS & struktur modal (styling dan HTML kosong)
- [ ] **Fase 2** – Card Quick Edit Panel di show.blade.php (tombol-tombol)
- [ ] **Fase 3** – 10 modal dengan form fields (copy dari edit.blade.php + sesuaikan)
- [ ] **Fase 4** – JavaScript: openModal/closeModal/escape/overlay-click
- [ ] **Fase 5** – Modal Lokasi dengan Leaflet map re-init
- [ ] **Fase 6** – Audit validasi controller (partial update)
- [ ] **Fase 7** – Test di mobile dan desktop
- [ ] **Fase 8** – Polish: animasi, loading state, flash message handling

---

## Open Questions

> [!IMPORTANT]
> Beberapa hal yang perlu kamu konfirmasi sebelum implementasi dimulai:

1. **Submit method**: Pakai form biasa (redirect + flash) atau AJAX (update tanpa reload)? AJAX lebih smooth tapi lebih banyak JS yang ditulis.

2. **Halaman edit lama**: Apakah tetap dipertahankan sebagai fallback, atau ingin dihapus tombolnya sama sekali dari sidebar?

3. **Urutan tombol di mobile**: Apakah 10 tombol ditampilkan semua dalam grid 2 kolom, atau ada grup/kategori (misal: "Konten", "Teknis")?

4. **Modal Lokasi dengan Leaflet**: Apakah fitur map picker tetap dipertahankan di modal, atau cukup input URL Google Maps saja (map picker membutuhkan space besar di modal)?

5. **Alpine.js di modal**: Section "Acara & Susunan" dan "Tanda Kasih" menggunakan Alpine.js untuk dynamic rows. Apakah fitur tambah/hapus rows tetap dipertahankan di dalam modal?

---

## Verification Plan

### Testing Manual
- Buka halaman show di desktop → tombol Quick Edit muncul di sidebar
- Buka halaman show di mobile → Quick Edit Panel muncul di posisi paling atas
- Klik setiap tombol → modal terbuka dengan animasi slide-up
- Klik overlay/tombol X/tekan Escape → modal tertutup
- Submit form di modal → data tersimpan, redirect ke show dengan flash success
- Test di layar mobile kecil (375px) → modal tidak overflow

### Automated Tests
```bash
php artisan test --filter InvitationUpdateTest
```

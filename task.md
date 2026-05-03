# Task List: Smart Fishpond Monitoring System
> Generated from PRD · Stack: Laravel + MySQL + ESP32  
> Format: `[ ]` belum · `[x]` selesai · `[-]` diblokir

---

## PHASE 0 — Project Setup
- [x] **0.1** Init project Laravel (latest stable) via `composer create-project`
- [x] **0.2** Setup `.env`: koneksi MySQL/MariaDB, APP_KEY, APP_URL
- [x] **0.3** Install package tambahan:
  - `maatwebsite/excel` → ekspor `.xlsx`
  - `laravel/sanctum` atau session auth (pilih sesuai kebutuhan)
- [x] **0.4** Setup Git repo + branching strategy (`main`, `dev`, `feature/*`)
- [ ] **0.5** Konfigurasi `config/cors.php` untuk akses API dari ESP32

---

## PHASE 1 — Database & Migration

- [x] **1.1** Buat migration `create_users_table` (sudah ada default Laravel, review kolom)
- [x] **1.2** Buat migration `create_kolams_table`
  - Kolom: `id`, `nama`, `lokasi`, `luas`, `timestamps`
- [x] **1.3** Buat migration `create_thresholds_table`
  - Kolom: `id`, `kolam_id` (FK → kolams), `ph_bawah`, `ph_atas`, `ketinggian_batas_bawah`, `ketinggian_batas_atas`, `suhu_bawah`, `suhu_atas`, `salinitas_bawah`, `salinitas_atas`, `timestamps`
- [x] **1.4** Buat migration `create_monitorings_table`
  - Kolom: `id`, `kolam_id` (FK → kolams), `ph`, `ketinggian_air`, `suhu_air`, `salinitas`, `rssi` (int), `delay` (int), `device_timestamp` (timestamp), `waktu_monitoring` (timestamp, default now), `timestamps`
- [x] **1.5** Buat migration `create_pemasukans_table`
  - Kolom: `id`, `deskripsi`, `jumlah` (decimal 15,2), `tanggal`, `waktu`, `timestamps`
- [x] **1.6** Buat migration `create_pengeluarans_table`
  - Kolom: `id`, `kategori` (enum: pakan, bibit, perawatan, lainnya), `deskripsi`, `jumlah` (decimal 15,2), `tanggal`, `timestamps`
- [x] **1.7** Jalankan `php artisan migrate`
- [x] **1.8** Buat Seeder untuk data dummy: `kolams`, `thresholds`, `monitorings` (minimal 100 record untuk testing chart)

---

## PHASE 2 — Models & Relationships

- [x] **2.1** Model `Kolam` → `hasMany(Monitoring)`, `hasOne(Threshold)`
- [x] **2.2** Model `Threshold` → `belongsTo(Kolam)`
- [x] **2.3** Model `Monitoring` → `belongsTo(Kolam)` · fillable: semua kolom sensor + FK
- [x] **2.4** Model `Pemasukan` → fillable: `deskripsi`, `jumlah`, `tanggal`, `waktu`
- [x] **2.5** Model `Pengeluaran` → fillable: `kategori`, `deskripsi`, `jumlah`, `tanggal`

---

## PHASE 3 — Authentication

- [x] **3.1** Gunakan Laravel Breeze / manual Auth controller
- [x] **3.2** Setup route group `middleware('auth')` untuk semua halaman web
- [x] **3.3** Buat view login (sesuaikan dengan UI style: navy `#1e3a8a`)
- [x] **3.4** Navbar dropdown "Logout" → `POST /logout`
- [x] **3.5** Proteksi route: redirect ke login jika belum autentikasi

---

## PHASE 4 — Layout & UI Shell

- [x] **4.1** Buat Blade layout utama (`layouts/app.blade.php`)
  - Sidebar fixed dengan menu: Dashboard, Tabel Data, Controlling, Management Kas
  - Navbar kanan: info Admin + dropdown Logout
  - Content area (yield)
- [x] **4.2** Implementasi color palette primary `#1e3a8a` (Deep Blue / Navy) via CSS / Tailwind config
- [x] **4.3** Pastikan layout responsive (minimal desktop-first)
- [x] **4.4** Aktifkan active state pada sidebar sesuai route aktif

---

## PHASE 5 — REST API (ESP32 Integration)

- [x] **5.1** Buat `Api\MonitoringController` dengan method `store()`
- [x] **5.2** Buat `MonitoringRequest` (Form Request) dengan validasi:
  - `ph`, `ketinggian_air`, `suhu_air`, `salinitas`, `rssi`, `device_timestamp` → required, numeric
  - Jika gagal → return `422 Unprocessable Entity` + pesan error
- [x] **5.3** Logic kalkulasi `delay`:
  ```
  delay = now() - device_timestamp  (dalam milidetik)
  ```
- [x] **5.4** Simpan data ke tabel `monitorings`
- [x] **5.5** Return `201 Created` + JSON `{"success": true, "message": "Data berhasil disimpan"}`
- [x] **5.6** Daftarkan route: `POST /api/monitoring` (tanpa middleware auth, gunakan throttle)
- [x] **5.7** Test endpoint dengan Postman / curl (simulasi payload ESP32)

---

## PHASE 6 — Dashboard

- [x] **6.1** Buat `DashboardController` → ambil data monitoring terbaru per kolam
- [x] **6.2** Buat 4 Metric Cards (pH, Air, Suhu, Salinitas) → nilai terbaru dari `monitorings`
- [x] **6.3** Integrasikan library chart (Chart.js / ApexCharts via CDN)
- [x] **6.4** Buat Line Chart dengan switcher metrik (pH / Air / Suhu / Salinitas)
  - Warna garis: `#1e3a8a`
- [x] **6.5** Implementasi **Filter Per Hari**:
  - Input: pilih tanggal
  - Query: `AVG()` per jam (`GROUP BY HOUR(waktu_monitoring)`)
- [x] **6.6** Implementasi **Filter Per Jam**:
  - Input: pilih tanggal + jam
  - Query: data detail dalam jam tersebut (tanpa agregasi)
- [x] **6.7** Buat endpoint AJAX / Livewire untuk refresh chart tanpa reload halaman
- [x] **6.8** Pastikan chart merespons switcher metrik secara dinamis

---

## PHASE 7 — Tabel Data Monitoring

- [x] **7.1** Buat `MonitoringController` → method `index()` dengan query builder
- [x] **7.2** Implementasi filter **Range Tanggal** (start date – end date)
- [x] **7.3** Implementasi **Pagination** 10 data per halaman (`->paginate(10)`)
- [x] **7.4** Tampilkan kolom: Waktu, Nama Kolam, pH, Air, Suhu, Salinitas, RSSI, Delay
- [x] **7.5** Buat `MonitoringExport` class (Maatwebsite Excel)
  - Export mengikuti filter tanggal aktif
- [x] **7.6** Tombol "Export Excel" → `GET /monitoring/export?start=...&end=...`
- [x] **7.7** Test ekspor dengan dataset > 1000 baris

---

## PHASE 8 — Controlling (Threshold Management)

- [x] **8.1** Buat `ThresholdController` → `index()` dan `update()`
- [x] **8.2** View Controlling: tampilkan form per parameter air (pH, Ketinggian, Suhu, Salinitas)
- [x] **8.3** Implementasi **Dual-dot Range Slider** (gunakan library: noUiSlider atau input[type=range] custom)
  - Setiap parameter: slider batas bawah & atas
  - Tampilkan nilai numerik secara real-time saat slider digeser
- [x] **8.4** Tombol "Save" → `PUT /controlling/{kolam_id}` → update tabel `thresholds`
- [x] **8.5** Validasi: `batas_bawah` harus < `batas_atas` untuk setiap parameter
- [x] **8.6** Flash message sukses/gagal setelah save

---

## PHASE 9 — Management Kas

- [x] **9.1** Buat `KasController` → method `index()`, `storePemasukan()`, `storePengeluaran()`
- [x] **9.2** Buat 3 Summary Cards:
  - **Saldo** = Total Pemasukan − Total Pengeluaran
  - **Total Pemasukan**
  - **Total Pengeluaran**
- [x] **9.3** Integrasikan chart keuangan (Bar Chart atau Pie Chart) → tren per bulan
  - Query: `SUM(jumlah)` GROUP BY bulan dari gabungan kedua tabel
- [x] **9.4** Tabel histori gabungan (`pemasukans` UNION `pengeluarans`) → tampilkan kolom: Tanggal, Tipe (Pemasukan/Pengeluaran), Kategori, Deskripsi, Jumlah
- [x] **9.5** Form tambah Pemasukan (modal atau inline)
- [x] **9.6** Form tambah Pengeluaran + field `kategori` (dropdown: pakan, bibit, perawatan, lainnya)
- [x] **9.7** Buat `KasExport` class → export laporan ke `.xlsx`
- [x] **9.8** Tombol "Export Laporan" → `GET /kas/export`

---

## PHASE 10 — Finishing & QA

- [x] **10.1** Review semua validasi form & API (edge case: nilai negatif, null, overflow)
- [x] **10.2** Cek konsistensi UI: warna, font, spacing sesuai guideline (`#1e3a8a`)
- [x] **10.3** Test skenario ESP32 offline → pastikan sistem tetap berjalan normal
- [x] **10.4** Test ekspor Excel: Tabel Data & Laporan Kas
- [x] **10.5** Test autentikasi: akses tanpa login → redirect ke halaman login
- [x] **10.6** Optimasi query: tambahkan index pada `kolam_id` dan `waktu_monitoring` di tabel `monitorings`
- [x] **10.7** Review CORS: pastikan hanya method POST dari device yang diizinkan di `/api/monitoring`
- [x] **10.8** Dokumentasi singkat: update `README.md` (cara install, migrate, seeder, env setup)

---

## Dependency Map

```
Phase 0 → Phase 1 → Phase 2
                         ↓
              Phase 3 → Phase 4
                         ↓
         ┌───────────────┼───────────────┐
         ↓               ↓               ↓
     Phase 5         Phase 6        Phase 7
   (API ESP32)     (Dashboard)    (Tabel Data)
                         ↓               ↓
                     Phase 8        Phase 9
                  (Controlling)   (Kas)
                         ↓
                     Phase 10 (QA)
```

---

> **Catatan:**  
> - Setiap phase sebaiknya di-*commit* ke branch `feature/<nama-phase>` sebelum di-merge ke `dev`  
> - Phase 5 (API) bisa dikerjakan paralel dengan Phase 3–4 karena tidak butuh UI  
> - Gunakan Postman Collection untuk dokumentasi & testing endpoint API
# Product Requirements Document (PRD): Smart Fishpond Monitoring System

## 1. Project Overview
Sistem terintegrasi untuk pemantauan kolam ikan secara *real-time* yang menghubungkan perangkat keras (ESP32) dengan dashboard web. Fokus utama sistem adalah visualisasi data sensor, analisis kesehatan koneksi (latensi), kontrol ambang batas parameter air, dan manajemen operasional keuangan (kas).

## 2. Technical Stack
* **Framework:** Laravel (Latest Stable)
* **Database:** MySQL/MariaDB
* **Communication:** REST API with JSON Validation
* **Device:** ESP32

## 3. Database Schema (Final Reference)

### 3.1 Users (`0001_01_01_000000_create_users_table.php`)
* `id`, `name`, `email`, `password`, `email_verified_at`, `remember_token`, `timestamps`.

### 3.2 Kolams (`2025_11_04_034444_create_kolams_table.php`)
* `id`, `nama`, `lokasi`, `luas`, `timestamps`.

### 3.3 Thresholds (`2025_11_04_034839_create_thresholds_table.php`)
* `id`, `kolam_id` (Foreign Key ke `kolams`).
* `ph_bawah`, `ph_atas`.
* `ketinggian_batas_bawah`, `ketinggian_batas_atas`.
* `suhu_bawah`, `suhu_atas`.
* `salinitas_bawah`, `salinitas_atas`.
* `timestamps`.

### 3.4 Monitorings (`2025_11_04_034923_create_monitorings_table.php`)
* `id`, `kolam_id` (Foreign Key ke `kolams`).
* `ph`, `ketinggian_air`, `suhu_air`, `salinitas`.
* `rssi` (Integer): Kekuatan sinyal dari ESP32.
* `delay` (Integer): Selisih milidetik antara `device_timestamp` dan waktu simpan di server.
* `device_timestamp` (Timestamp): Waktu pengiriman data dari ESP32.
* `waktu_monitoring` (Timestamp): Default current timestamp.
* `timestamps`.

### 3.5 Pemasukans (`2025_11_04_035509_create_pemasukans_table.php`)
* `id`, `deskripsi`, `jumlah` (Decimal 15,2), `tanggal`, `waktu`, `timestamps`.

### 3.6 Pengeluarans (`2025_11_04_035612_create_pengeluarans_table.php`)
* `id`, `kategori` (Enum: pakan, bibit, perawatan, lainnya).
* `deskripsi`, `jumlah` (Decimal 15,2), `tanggal`, `timestamps`.

## 4. Functional Requirements

### 4.1 Dashboard
* **Top Section:** 4 Metric Cards menampilkan data terbaru untuk pH, Air, Suhu, dan Salinitas.
* **Main Chart:** Line Chart dengan switcher untuk memilih metrik.
    * **Filter Per Hari:** Menampilkan rata-rata data per jam jika tidak memilih jam spesifik.
    * **Filter Per Jam:** Menampilkan data detail pada jam di hari tersebut.
* **UI Style:** Visual bersih dengan aksen Biru Tua pada garis grafik.

### 4.2 Tabel Data Monitoring
* **Features:** Pagination (10 data per halaman) dan Filter Range Tanggal (Start - End).
* **Columns:** Waktu, Nama Kolam, pH, Air, Suhu, Salinitas, RSSI, Delay.
* **Export:** Dukungan ekspor data ke format `.xlsx` (Excel).

### 4.3 Controlling
* **Component:** Dual-dot Range Slider untuk mengatur `batas_bawah` dan `batas_atas` setiap parameter air.
* **Action:** Tombol "Save" untuk memperbarui entri pada tabel `thresholds`.

### 4.4 Management Kas
* **Summary Cards:** Saldo (Total Pemasukan - Total Pengeluaran), Total Pemasukan, dan Total Pengeluaran.
* **Chart:** Visualisasi Bar atau Pie untuk tren keuangan bulanan.
* **Table:** Histori transaksi gabungan dari tabel `pemasukans` dan `pengeluarans`.
* **Export:** Dukungan ekspor laporan kas ke `.xlsx`.

## 5. REST API Standard (ESP32 Integration)
* **Endpoint:** `POST /api/monitoring`.
* **Validation:** * Input harus berupa angka (numeric), jika gagal return `422 Unprocessable Entity`.
    * Jika berhasil, return `201 Created` dengan JSON `{"success": true, "message": "..."}`.
* **Logic:** `delay` dihitung secara otomatis dengan rumus `now() - device_timestamp`.

## 6. UI/UX Design Guidelines
* **Color Palette:** Primary Color `#1e3a8a` (Deep Blue / Navy).
* **Layout:**
    * **Sidebar:** Fixed sidebar dengan menu navigasi: Dashboard, Tabel Data, Controlling, dan Management Kas.
    * **Navbar:** Informasi Admin di sisi kanan dengan dropdown menu "Logout".
* **General Style:** Clean Professional UI, fokus pada keterbacaan data.
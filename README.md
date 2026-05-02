# Smart Fishpond Monitoring System

Sistem pemantauan kolam ikan pintar berbasis IoT (ESP32) dan Laravel.

## Fitur Utama
1. **Dashboard Real-time**: Visualisasi data sensor (pH, Ketinggian Air, Suhu, Salinitas) menggunakan Chart.js.
2. **Tabel Data Monitoring**: Riwayat data sensor lengkap dengan filter tanggal dan fitur ekspor Excel.
3. **Controlling Threshold**: Pengaturan ambang batas parameter air per kolam menggunakan Dual-dot Range Slider.
4. **Manajemen Kas**: Pencatatan pemasukan dan pengeluaran operasional tambak dengan laporan grafik dan ekspor Excel.
5. **API Integration**: Endpoint khusus untuk pengiriman data dari ESP32 dengan kalkulasi delay latensi.

## Integrasi ESP32 (API)

**Endpoint:** `POST /api/monitoring`  
**Rate Limit:** 60 request / menit

**Payload JSON:**
```json
{
    "kolam_id": 1,
    "ph": 7.2,
    "ketinggian_air": 65.5,
    "suhu_air": 28.4,
    "salinitas": 15.0,
    "rssi": -65,
    "device_timestamp": "2026-05-02T12:00:00Z"
}
```

## Teknologi
- Laravel 12
- Tailwind CSS (Navy Theme)
- Chart.js (Visualisasi)
- noUiSlider (Controlling)
- Maatwebsite Excel (Laporan)
- MySQL (Database)

## Instalasi
1. Clone repository
2. `composer install`
3. `npm install && npm run build`
4. Konfigurasi `.env` (Database)
5. `php artisan migrate --seed`
6. `php artisan serve`

---
© 2026 Omahiot

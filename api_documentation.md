# Dokumentasi API Monitoring Ikan

API ini digunakan untuk mengirimkan data sensor dari perangkat IoT ke sistem *Monitoring Ikan* serta mengambil status kontrol (controlling) untuk aktuator. 

> **PENTING:** Semua endpoint (kecuali Login) wajib menyertakan header `Authorization: Bearer <token>` yang didapatkan dari endpoint Login.

---

## 1. Endpoint: Login (Mendapatkan Token)

- **URL:** `/api/login`
- **Method:** `POST`
- **Content-Type:** `application/json`
- **Accept:** `application/json`

### Deskripsi
Digunakan untuk melakukan autentikasi pengguna dan mendapatkan **Bearer Token** (Sanctum Token) yang akan digunakan untuk mengakses endpoint lainnya.

### Parameter Request (JSON)

| Parameter  | Tipe Data | Wajib | Deskripsi                        |
| ---------- | --------- | ----- | -------------------------------- |
| `email`    | String    | Ya    | Email pengguna yang terdaftar.   |
| `password` | String    | Ya    | Password pengguna.               |

### Contoh Request (JSON)

```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

### Contoh Response Sukses (HTTP 200 OK)

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin",
            "email": "admin@example.com",
            "created_at": "...",
            "updated_at": "..."
        },
        "token": "1|abcdef1234567890abcdef1234567890"
    }
}
```
*(Gunakan nilai `token` di atas untuk header `Authorization` pada request berikutnya)*

---

## 2. Endpoint: Upload Data Sensor (Monitoring)

- **URL:** `/api/monitoring`
- **Method:** `POST`
- **Content-Type:** `application/json`
- **Accept:** `application/json`
- **Header Tambahan:** `Authorization: Bearer <token_anda>`

### Deskripsi
Digunakan untuk menyimpan data sensor yang dikirimkan oleh perangkat (misal: ESP32/ESP8266) ke dalam database. Sistem akan secara otomatis menghitung *delay* (selisih waktu perangkat dengan waktu server) saat data diterima.

### Parameter Request (JSON)

| Parameter          | Tipe Data | Wajib | Deskripsi                                                                 |
| ------------------ | --------- | ----- | ------------------------------------------------------------------------- |
| `kolam_id`         | Integer   | Ya    | ID kolam tempat perangkat dipasang (harus ada di tabel `kolams`).       |
| `ph`               | Float     | Ya    | Nilai pH air (0 - 14).                                                    |
| `ketinggian_air`   | Float     | Ya    | Ketinggian air dalam sentimeter atau satuan lain (minimal 0).             |
| `suhu_air`         | Float     | Ya    | Suhu air dalam Celcius (antara -50 sampai 100).                           |
| `salinitas`        | Float     | Ya    | Tingkat salinitas/kadar garam air (minimal 0).                            |
| `rssi`             | Float     | Ya    | Kekuatan sinyal WiFi perangkat (antara -150 sampai 0).                    |
| `snr`              | Float     | Ya    | Signal-to-Noise Ratio perangkat.                                         |
| `pdr`              | Integer   | Ya    | Packet Delivery Ratio perangkat dalam persen (0 - 100).                   |
| `device_timestamp` | String    | Ya    | Waktu pengambilan data pada perangkat dengan format `YYYY-MM-DD HH:mm:ss`.|

### Contoh Request (JSON)

```json
{
    "kolam_id": 1,
    "ph": 7.2,
    "ketinggian_air": 85.5,
    "suhu_air": 28.4,
    "salinitas": 12.0,
    "rssi": -65,
    "snr": 10.5,
    "pdr": 98,
    "device_timestamp": "2026-05-03 13:15:00"
}
```

### Contoh Penggunaan menggunakan `curl`

```bash
curl -X POST http://domain-anda.com/api/monitoring \
     -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
           "kolam_id": 1,
           "ph": 7.2,
           "ketinggian_air": 85.5,
           "suhu_air": 28.4,
           "salinitas": 12.0,
           "rssi": -65,
           "snr": 10.5,
           "pdr": 98,
           "device_timestamp": "2026-05-03 13:15:00"
         }'
```

### Contoh Response Sukses (HTTP 201 Created)

```json
{
    "success": true,
    "message": "Data berhasil disimpan",
    "data": {
        "kolam_id": 1,
        "ph": 7.2,
        "ketinggian_air": 85.5,
        "suhu_air": 28.4,
        "salinitas": 12.0,
        "rssi": -65,
        "snr": 10.5,
        "pdr": 98,
        "device_timestamp": "2026-05-03 13:15:00",
        "delay": 1205,
        "waktu_monitoring": "2026-05-03T06:15:01.205000Z",
        "id": 15,
        "created_at": "2026-05-03T06:15:01.000000Z",
        "updated_at": "2026-05-03T06:15:01.000000Z"
    }
}
```

---

## 3. Endpoint: Mengambil Status Kontrol (Controlling)

- **URL:** `/api/controlling/{kolam_id}`
- **Method:** `GET`
- **Accept:** `application/json`
- **Header Tambahan:** `Authorization: Bearer <token_anda>`

### Deskripsi
Digunakan oleh perangkat IoT untuk mengambil perintah kontrol aktuator (misalnya relay untuk pompa air, aerator, dll) berdasarkan ID kolam.

### Parameter URL (Path Variable)

| Parameter  | Tipe Data | Wajib | Deskripsi                        |
| ---------- | --------- | ----- | -------------------------------- |
| `kolam_id` | Integer   | Ya    | ID kolam yang ingin dicek statusnya. |

### Contoh Request `curl`

```bash
curl -X GET http://domain-anda.com/api/controlling/1 \
     -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890" \
     -H "Accept: application/json"
```

### Contoh Response Sukses (HTTP 200 OK)

```json
{
    "success": true,
    "message": "Data kontrol berhasil diambil",
    "data": {
        "pompa_air": 1,
        "aerator": 0,
        "auto_feeder": 1
    }
}
```
*(Struktur objek `data` dapat menyesuaikan dengan *field* yang ada di database tabel kontroling Anda.)*

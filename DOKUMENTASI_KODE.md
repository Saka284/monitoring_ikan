# Dokumentasi Kode: Deep Dive (Sistem Monitoring Ikan)

Dokumentasi ini menjelaskan implementasi kode secara mendalam untuk membantu pengembang memahami bagaimana fitur-fitur dalam aplikasi ini bekerja di balik layar.

---

## 1. Database & Migrasi (The Blueprint)
Semua bermula dari struktur database. Kita ambil contoh tabel `monitorings`.

**File:** `database/migrations/2026_05_02_041856_create_monitorings_table.php`

```php
Schema::create('monitorings', function (Blueprint $table) {
    $table->id();
    // Menghubungkan ke tabel kolams. Jika kolam dihapus, data monitoring ikut terhapus (cascade).
    $table->foreignId('kolam_id')->constrained('kolams')->onDelete('cascade');
    
    // Data sensor menggunakan tipe float untuk presisi desimal
    $table->float('ph');
    $table->float('ketinggian_air');
    $table->float('suhu_air');
    $table->float('salinitas');
    
    // Data network & metadata
    $table->integer('rssi'); // Kekuatan sinyal
    $table->integer('delay'); // Waktu pengiriman data
    $table->timestamp('device_timestamp'); // Waktu dari alat IoT
    $table->timestamp('waktu_monitoring')->useCurrent(); // Waktu server saat data masuk
    $table->timestamps(); // created_at & updated_at otomatis Laravel
});
```

---

## 2. Model: Logika Data
Model bukan hanya representasi tabel, tapi juga tempat mendefinisikan relasi dan format data.

**File:** `app/Models/Monitoring.php`

```php
class Monitoring extends Model
{
    // Mass Assignment: Kolom yang boleh diisi secara massal (melalui create/update)
    protected $fillable = [
        'kolam_id', 'ph', 'ketinggian_air', 'suhu_air', 'salinitas', 
        'rssi', 'snr', 'pdr', 'delay', 'device_timestamp', 'waktu_monitoring'
    ];

    // Casting: Mengubah string dari DB menjadi object Carbon (datetime) secara otomatis
    protected $casts = [
        'waktu_monitoring' => 'datetime',
        'device_timestamp' => 'datetime',
    ];

    // Relasi: Setiap data monitoring dimiliki oleh satu Kolam (Many to One)
    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}
```

---

## 3. Controller: Otak Sistem
Controller membagi tugas antara Web (Tampilan) dan API (Penerimaan Data).

### A. API (Menerima data dari alat IoT)
**File:** `app/Http/Controllers/Api/MonitoringController.php`

```php
public function store(MonitoringRequest $request)
{
    // 1. Ambil data yang sudah divalidasi (cek tipe data, wajib diisi, dll)
    $validated = $request->validated();
    
    // 2. Logika Hitung Delay:
    // Selisih antara waktu di alat IoT (device_timestamp) dengan waktu server saat ini (now).
    $deviceTime = Carbon::parse($validated['device_timestamp']);
    $now = now();
    $delay = (int) abs($now->diffInMilliseconds($deviceTime));
    
    // 3. Simpan ke database
    $monitoring = Monitoring::create(array_merge($validated, [
        'delay' => $delay,
        'waktu_monitoring' => $now->format('Y-m-d H:i:s')
    ]));
    
    // 4. Return response JSON (format yang dimengerti alat IoT)
    return response()->json(['success' => true, 'data' => $monitoring], 201);
}
```

### B. Web (Menampilkan data ke user)
**File:** `app/Http/Controllers/MonitoringController.php`

```php
public function index(Request $request)
{
    // Query builder: Ambil monitoring terbaru beserta data kolam & threshold-nya (Eager Loading)
    $query = Monitoring::with('kolam.threshold')->latest('waktu_monitoring');

    // Filter Tanggal (jika user memilih rentang waktu di UI)
    if ($request->filled('start_date')) {
        $query->whereDate('waktu_monitoring', '>=', $request->start_date);
    }

    // Pagination: Menampilkan 10 data per halaman agar loading tidak berat
    $monitorings = $query->paginate(10)->withQueryString();
    
    return view('monitoring.index', compact('monitorings'));
}
```

---

## 4. Route: Gerbang Masuk
Route menentukan URL mana yang akan memanggil Controller yang mana.

**Web (`routes/web.php`):**
```php
// URL: domain.com/monitoring -> Memanggil MonitoringController fungsi index
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
```

**API (`routes/api.php`):**
```php
// URL: domain.com/api/monitoring -> Menggunakan middleware 'auth:sanctum' (keamanan token)
Route::post('/monitoring', [MonitoringController::class, 'store'])->middleware('auth:sanctum');
```

---

## 5. View: Antarmuka Blade
Di View, kita menggunakan data yang dikirim Controller.

**Contoh Logic di Blade:**
```html
@foreach ($monitorings as $item)
    <tr>
        <td>{{ $item->waktu_monitoring->format('d/m/Y H:i') }}</td>
        <td>{{ $item->ph }}</td>
        <td>
            <!-- Contoh indikator warna berdasarkan ambang batas (threshold) -->
            @if($item->ph < $item->kolam->threshold->ph_min)
                <span class="text-red-500">Terlalu Rendah</span>
            @else
                <span class="text-green-500">Normal</span>
            @endif
        </td>
    </tr>
@endforeach
```

---

## Rangkuman "Nyambungnya Ke Mana?"

1. **Request Masuk** (Web/IoT) -> **Route**
2. **Route** memanggil -> **Controller**
3. **Controller** validasi data & panggil -> **Model**
4. **Model** bicara ke -> **Database** (Migration menentukan strukturnya)
5. **Model** kirim data balik ke -> **Controller**
6. **Controller** kirim data ke -> **View** (Web) atau **JSON** (API)
7. **View** merender HTML untuk dilihat -> **User**

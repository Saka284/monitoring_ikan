<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\MonitoringRequest;
use App\Models\Monitoring;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    /**
     * Fungsi untuk menyimpan data monitoring yang dikirim dari alat IoT.
     * Menggunakan MonitoringRequest untuk validasi data otomatis.
     */
    public function store(MonitoringRequest $request)
    {
        // 1. Ambil data yang sudah lolos validasi
        $validated = $request->validated();
        
        // 2. Olah waktu: ambil waktu dari alat dan waktu server saat ini
        $deviceTime = Carbon::parse($validated['device_timestamp']);
        $now = now();
        
        // 3. Hitung delay: selisih waktu alat dan waktu server dalam milidetik
        // abs() digunakan agar hasilnya selalu positif
        $delay = (int) abs($now->diffInMilliseconds($deviceTime));
        
        // 4. Simpan data ke database
        $monitoring = Monitoring::create(array_merge($validated, [
            'device_timestamp' => $deviceTime->format('Y-m-d H:i:s'),
            'delay' => $delay,
            'waktu_monitoring' => $now->format('Y-m-d H:i:s')
        ]));
        
        // 5. Kirim respon balik ke alat IoT dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $monitoring
        ], 201);
    }
}

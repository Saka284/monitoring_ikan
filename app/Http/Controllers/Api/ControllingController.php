<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Threshold;
use Illuminate\Http\Request;

class ControllingController extends Controller
{
    /**
     * Fungsi untuk mengambil data ambang batas (threshold) untuk alat IoT.
     * Alat IoT akan memanggil ini untuk tahu kapan harus menghidupkan/mematikan alat (pompa/heater).
     */
    public function show($kolam_id)
    {
        // 1. Cari data threshold berdasarkan ID kolam
        $threshold = Threshold::where('kolam_id', $kolam_id)->first();

        // 2. Jika tidak ditemukan, kirim pesan error 404
        if (!$threshold) {
            return response()->json([
                'success' => false,
                'message' => 'Data controlling tidak ditemukan untuk kolam ini',
                'data' => null
            ], 404);
        }

        // 3. Jika ditemukan, kirim data batas sensor dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Data controlling berhasil diambil',
            'data' => [
                'ph_bawah' => $threshold->ph_bawah,
                'ph_atas' => $threshold->ph_atas,
                'ketinggian_batas_bawah' => $threshold->ketinggian_batas_bawah,
                'ketinggian_batas_atas' => $threshold->ketinggian_batas_atas,
                'suhu_bawah' => $threshold->suhu_bawah,
                'suhu_atas' => $threshold->suhu_atas,
                'salinitas_bawah' => $threshold->salinitas_bawah,
                'salinitas_atas' => $threshold->salinitas_atas,
            ]
        ]);
    }
}

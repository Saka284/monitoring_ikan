<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Threshold;
use App\Models\Kolam;

class ThresholdController extends Controller
{
    /**
     * Menampilkan halaman pengaturan ambang batas (threshold) sensor.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua data kolam untuk pilihan di tab/dropdown
        $kolams = Kolam::all();
        
        // 2. Tentukan kolam mana yang sedang dipilih (default: kolam pertama)
        $selectedKolamId = $request->get('kolam_id', $kolams->first()?->id);
        
        // 3. Ambil data threshold untuk kolam yang dipilih
        $threshold = Threshold::where('kolam_id', $selectedKolamId)->first();

        // 4. Kirim data ke view
        return view('controlling.index', compact('kolams', 'threshold', 'selectedKolamId'));
    }

    /**
     * Menyimpan atau memperbarui data ambang batas.
     */
    public function update(Request $request, $kolamId)
    {
        // 1. Validasi input dari form: harus angka dan batas bawah harus lebih kecil dari batas atas
        $validated = $request->validate([
            'ph_bawah' => 'required|numeric|lt:ph_atas',
            'ph_atas' => 'required|numeric',
            'ketinggian_batas_bawah' => 'required|numeric|lt:ketinggian_batas_atas',
            'ketinggian_batas_atas' => 'required|numeric',
            'suhu_bawah' => 'required|numeric|lt:suhu_atas',
            'suhu_atas' => 'required|numeric',
            'salinitas_bawah' => 'required|numeric|lt:salinitas_atas',
            'salinitas_atas' => 'required|numeric',
        ]);

        // 2. updateOrCreate: Cari berdasarkan kolam_id, jika ada maka update, jika tidak ada maka buat baru
        Threshold::updateOrCreate(
            ['kolam_id' => $kolamId],
            $validated
        );

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Threshold berhasil diperbarui');
    }
}

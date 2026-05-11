<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Monitoring;
use App\Models\Kolam;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringExport;

class MonitoringController extends Controller
{
    /**
     * Menampilkan halaman daftar riwayat monitoring sensor di Web.
     */
    public function index(Request $request)
    {
        // 1. Buat query dasar: ambil data monitoring beserta relasi kolam dan threshold-nya
        // latest('waktu_monitoring') mengurutkan dari yang terbaru
        $query = Monitoring::with('kolam.threshold')->latest('waktu_monitoring');

        // 2. Cek apakah ada filter tanggal mulai dari user
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_monitoring', '>=', $request->start_date);
        }

        // 3. Cek apakah ada filter tanggal akhir dari user
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_monitoring', '<=', $request->end_date);
        }

        // 4. Cek apakah user memilih kolam tertentu
        if ($request->filled('kolam_id')) {
            $query->where('kolam_id', $request->kolam_id);
        }

        // 5. Jalankan query dengan pagination (10 data per halaman)
        $monitorings = $query->paginate(10)->withQueryString();
        
        // 6. Ambil semua data kolam untuk pilihan di dropdown filter
        $kolams = Kolam::all();

        // 7. Hitung statistik untuk hari ini
        $totalToday = Monitoring::whereDate('waktu_monitoring', now()->toDateString())->count();

        // 8. Kirim data ke file view resources/views/monitoring/index.blade.php
        return view('monitoring.index', compact('monitorings', 'kolams', 'totalToday'));
    }

    /**
     * Fungsi untuk mendownload data monitoring ke format Excel.
     */
    public function export(Request $request)
    {
        // Menggunakan library Excel untuk membuat file .xlsx berdasarkan data yang difilter
        return Excel::download(new MonitoringExport($request), 'data_monitoring.xlsx');
    }
}

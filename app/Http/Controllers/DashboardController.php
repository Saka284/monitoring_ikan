<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Monitoring;
use App\Models\Kolam;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Dashboard utama.
     */
    public function index()
    {
        // 1. Ambil data monitoring paling terbaru untuk ditampilkan di kartu ringkasan
        $latest = Monitoring::with('kolam')->latest('waktu_monitoring')->first();
        
        // 2. Ambil daftar kolam untuk filter chart
        $kolams = Kolam::all();
        
        // 3. Render view dashboard.blade.php
        return view('dashboard', compact('latest', 'kolams'));
    }

    /**
     * Mengambil data untuk grafik (Chart.js) secara dinamis via AJAX.
     */
    public function chartData(Request $request)
    {
        // 1. Parameter filter dari request (metrik apa, kolam mana, tanggal berapa)
        $metric = $request->get('metric', 'ph');
        $kolamId = $request->get('kolam_id');
        $date = $request->get('date', now()->toDateString());
        $hour = $request->get('hour');

        // 2. Query dasar berdasarkan tanggal
        $query = Monitoring::whereDate('waktu_monitoring', $date);

        // 3. Tambahkan filter kolam jika ada
        if ($kolamId) {
            $query->where('kolam_id', $kolamId);
        }

        // 4. Logika pengambilan data
        if ($hour !== null && $hour !== '') {
            // Jika user memilih jam tertentu, tampilkan detail per menit di jam tersebut
            $data = $query->whereRaw('HOUR(waktu_monitoring) = ?', [$hour])
                ->orderBy('waktu_monitoring', 'asc')
                ->get(['waktu_monitoring', $metric]);
            
            $labels = $data->map(fn($item) => Carbon::parse($item->waktu_monitoring)->format('H:i'));
            $values = $data->pluck($metric);
        } else {
            // Jika tidak ada jam terpilih, tampilkan rata-rata per jam (agregasi)
            $data = $query->selectRaw("HOUR(waktu_monitoring) as hour, AVG($metric) as avg_value")
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();
            
            $labels = $data->map(fn($item) => sprintf('%02d:00', $item->hour));
            $values = $data->pluck('avg_value');
        }

        // 5. Kembalikan data dalam format JSON untuk dibaca oleh JavaScript di frontend
        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'metric' => $metric
        ]);
    }

    /**
     * Mengambil data terbaru secara real-time (AJAX polling).
     */
    public function latestData()
    {
        $latest = Monitoring::with('kolam')->latest('waktu_monitoring')->first();
        
        return response()->json($latest);
    }
}

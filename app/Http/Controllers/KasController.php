<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KasExport;

class KasController extends Controller
{
    /**
     * Menampilkan halaman manajemen kas (keuangan).
     */
    public function index()
    {
        // 1. Hitung total uang masuk, uang keluar, dan saldo saat ini
        $totalPemasukan = Pemasukan::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 2. Gabungkan riwayat pemasukan dan pengeluaran menjadi satu daftar (Union)
        // Kita beri label 'tipe' agar tahu mana yang masuk dan mana yang keluar
        $pemasukanHistory = DB::table('pemasukans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pemasukan' as tipe"), DB::raw("NULL as kategori"));
        
        $history = DB::table('pengeluarans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pengeluaran' as tipe"), 'kategori')
            ->union($pemasukanHistory)
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        // 3. Ambil data bulanan untuk grafik batang (Bar Chart)
        $monthlyIncome = Pemasukan::selectRaw("MONTH(tanggal) as month, SUM(jumlah) as total")
            ->groupBy('month')->get();
        $monthlyExpense = Pengeluaran::selectRaw("MONTH(tanggal) as month, SUM(jumlah) as total")
            ->groupBy('month')->get();

        // 4. Ambil data pengeluaran per kategori untuk grafik lingkaran (Pie Chart)
        $expenseByCategory = Pengeluaran::selectRaw("kategori, SUM(jumlah) as total")
            ->groupBy('kategori')->get();

        // 5. Kirim semua variabel ke view kas/index.blade.php
        return view('kas.index', compact('totalPemasukan', 'totalPengeluaran', 'saldo', 'history', 'monthlyIncome', 'monthlyExpense', 'expenseByCategory'));
    }

    /**
     * Fungsi untuk menyimpan data pemasukan baru.
     */
    public function storePemasukan(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'waktu' => 'required',
        ]);

        Pemasukan::create($validated);
        return redirect()->back()->with('success', 'Pemasukan berhasil ditambahkan');
    }

    /**
     * Fungsi untuk menyimpan data pengeluaran baru.
     */
    public function storePengeluaran(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'kategori' => 'required|in:pakan,bibit,perawatan,lainnya',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create($validated);
        return redirect()->back()->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    /**
     * Mendownload laporan kas dalam format Excel.
     */
    public function export()
    {
        return Excel::download(new KasExport, 'laporan_kas.xlsx');
    }
}

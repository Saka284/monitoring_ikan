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
    public function index()
    {
        $totalPemasukan = Pemasukan::sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $pemasukanHistory = DB::table('pemasukans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pemasukan' as tipe"), DB::raw("NULL as kategori"));
        
        $history = DB::table('pengeluarans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pengeluaran' as tipe"), 'kategori')
            ->union($pemasukanHistory)
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        // Chart data (Monthly)
        $monthlyIncome = Pemasukan::selectRaw("MONTH(tanggal) as month, SUM(jumlah) as total")
            ->groupBy('month')->get();
        $monthlyExpense = Pengeluaran::selectRaw("MONTH(tanggal) as month, SUM(jumlah) as total")
            ->groupBy('month')->get();

        // Pie Chart data (Expense by category)
        $expenseByCategory = Pengeluaran::selectRaw("kategori, SUM(jumlah) as total")
            ->groupBy('kategori')->get();

        return view('kas.index', compact('totalPemasukan', 'totalPengeluaran', 'saldo', 'history', 'monthlyIncome', 'monthlyExpense', 'expenseByCategory'));
    }

    public function storePemasukan(Request $request)
    {
        $validated = $request->validate([
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'waktu' => 'required',
        ]);

        Pemasukan::create($validated);
        return redirect()->back()->with('success', 'Pemasukan berhasil ditambahkan');
    }

    public function storePengeluaran(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:pakan,bibit,perawatan,lainnya',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create($validated);
        return redirect()->back()->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function export()
    {
        return Excel::download(new KasExport, 'laporan_kas.xlsx');
    }
}

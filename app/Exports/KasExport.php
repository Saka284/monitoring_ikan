<?php

namespace App\Exports;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class KasExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $pemasukanHistory = DB::table('pemasukans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pemasukan' as tipe"), DB::raw("NULL as kategori"));
        
        return DB::table('pengeluarans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pengeluaran' as tipe"), 'kategori')
            ->union($pemasukanHistory)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Kategori',
            'Deskripsi',
            'Jumlah'
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            ucfirst($row->tipe),
            $row->kategori ?? '-',
            $row->deskripsi,
            $row->jumlah
        ];
    }
}

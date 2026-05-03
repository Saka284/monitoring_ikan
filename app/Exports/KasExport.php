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
        
        $data = DB::table('pengeluarans')
            ->select('id', 'deskripsi', 'jumlah', 'tanggal', DB::raw("'pengeluaran' as tipe"), 'kategori')
            ->union($pemasukanHistory)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPemasukan = DB::table('pemasukans')->sum('jumlah');
        $totalPengeluaran = DB::table('pengeluarans')->sum('jumlah');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        $data->push((object)[
            'tanggal' => '',
            'tipe' => '',
            'kategori' => '',
            'deskripsi' => 'SALDO AKHIR',
            'jumlah' => $saldoAkhir
        ]);

        return $data;
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
        if ($row->deskripsi === 'SALDO AKHIR') {
            return [
                '',
                '',
                '',
                'SALDO AKHIR',
                $row->jumlah
            ];
        }

        return [
            $row->tanggal,
            ucfirst($row->tipe),
            $row->kategori ?? '-',
            $row->deskripsi,
            $row->jumlah
        ];
    }
}

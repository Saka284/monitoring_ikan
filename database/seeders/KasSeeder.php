<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pemasukan
        \App\Models\Pemasukan::create([
            'deskripsi' => 'Penjualan Ikan Lele',
            'jumlah' => 2500000,
            'tanggal' => now()->subDays(5),
            'waktu' => '09:00:00'
        ]);

        \App\Models\Pemasukan::create([
            'deskripsi' => 'Penjualan Ikan Nila',
            'jumlah' => 1800000,
            'tanggal' => now()->subDays(2),
            'waktu' => '10:30:00'
        ]);

        // Pengeluaran
        \App\Models\Pengeluaran::create([
            'kategori' => 'pakan',
            'deskripsi' => 'Beli Pakan 5 Karung',
            'jumlah' => 750000,
            'tanggal' => now()->subDays(7),
        ]);

        \App\Models\Pengeluaran::create([
            'kategori' => 'bibit',
            'deskripsi' => 'Bibit Nila Unggul',
            'jumlah' => 500000,
            'tanggal' => now()->subDays(6),
        ]);
    }
}

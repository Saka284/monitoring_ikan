<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KolamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Kolam::create([
            'nama' => 'Kolam A1',
            'lokasi' => 'Sektor Utara',
            'luas' => '50m2'
        ]);

        \App\Models\Kolam::create([
            'nama' => 'Kolam B2',
            'lokasi' => 'Sektor Selatan',
            'luas' => '75m2'
        ]);
    }
}

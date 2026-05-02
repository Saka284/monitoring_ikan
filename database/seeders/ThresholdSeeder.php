<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThresholdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kolams = \App\Models\Kolam::all();

        foreach ($kolams as $kolam) {
            \App\Models\Threshold::create([
                'kolam_id' => $kolam->id,
                'ph_bawah' => 6.5,
                'ph_atas' => 8.5,
                'ketinggian_batas_bawah' => 40,
                'ketinggian_batas_atas' => 80,
                'suhu_bawah' => 25,
                'suhu_atas' => 32,
                'salinitas_bawah' => 10,
                'salinitas_atas' => 20,
            ]);
        }
    }
}

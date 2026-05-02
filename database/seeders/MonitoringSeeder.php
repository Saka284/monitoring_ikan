<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kolams = \App\Models\Kolam::all();
        $now = now();

        foreach ($kolams as $kolam) {
            for ($i = 100; $i >= 0; $i--) {
                $timestamp = $now->copy()->subMinutes($i * 15); // Every 15 minutes
                \App\Models\Monitoring::create([
                    'kolam_id' => $kolam->id,
                    'ph' => rand(60, 90) / 10,
                    'ketinggian_air' => rand(30, 90),
                    'suhu_air' => rand(24, 33),
                    'salinitas' => rand(5, 25),
                    'rssi' => rand(-90, -30),
                    'delay' => rand(50, 500),
                    'device_timestamp' => $timestamp,
                    'waktu_monitoring' => $timestamp,
                ]);
            }
        }
    }
}

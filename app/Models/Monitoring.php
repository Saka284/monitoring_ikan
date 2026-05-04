<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    /** @use HasFactory<\Database\Factories\MonitoringFactory> */
    use HasFactory;

    protected $fillable = [
        'kolam_id', 'ph', 'ketinggian_air', 'suhu_air', 'salinitas', 
        'rssi', 'snr', 'pdr', 'delay', 'device_timestamp', 'waktu_monitoring'
    ];

    protected $casts = [
        'waktu_monitoring' => 'datetime',
        'device_timestamp' => 'datetime',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

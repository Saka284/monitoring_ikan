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
        'rssi', 'delay', 'device_timestamp', 'waktu_monitoring'
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}

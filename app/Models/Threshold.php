<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    /** @use HasFactory<\Database\Factories\ThresholdFactory> */
    use HasFactory;

    protected $fillable = [
        'kolam_id', 'ph_bawah', 'ph_atas', 
        'ketinggian_batas_bawah', 'ketinggian_batas_atas', 
        'suhu_bawah', 'suhu_atas', 
        'salinitas_bawah', 'salinitas_atas'
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kolam extends Model
{
    /** @use HasFactory<\Database\Factories\KolamFactory> */
    use HasFactory;

    protected $fillable = ['nama', 'lokasi', 'luas'];

    public function monitorings()
    {
        return $this->hasMany(Monitoring::class);
    }

    public function threshold()
    {
        return $this->hasOne(Threshold::class);
    }
}

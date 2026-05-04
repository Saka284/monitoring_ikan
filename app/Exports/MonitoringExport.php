<?php

namespace App\Exports;

use App\Models\Monitoring;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class MonitoringExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Monitoring::query()->with('kolam');

        if ($this->request->filled('start_date')) {
            $query->whereDate('waktu_monitoring', '>=', $this->request->start_date);
        }

        if ($this->request->filled('end_date')) {
            $query->whereDate('waktu_monitoring', '<=', $this->request->end_date);
        }

        if ($this->request->filled('kolam_id')) {
            $query->where('kolam_id', $this->request->kolam_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Waktu',
            'Kolam',
            'pH',
            'Ketinggian Air (cm)',
            'Suhu (°C)',
            'Salinitas (ppt)',
            'RSSI',
            'SNR',
            'PDR (%)',
            'Delay (ms)'
        ];
    }

    public function map($monitoring): array
    {
        return [
            $monitoring->waktu_monitoring,
            $monitoring->kolam->nama,
            $monitoring->ph,
            $monitoring->ketinggian_air,
            $monitoring->suhu_air,
            $monitoring->salinitas,
            $monitoring->rssi,
            $monitoring->snr,
            $monitoring->pdr,
            $monitoring->delay,
        ];
    }
}

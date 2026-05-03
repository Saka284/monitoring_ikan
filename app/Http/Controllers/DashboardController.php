<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Monitoring;
use App\Models\Kolam;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = Monitoring::with('kolam')->latest('waktu_monitoring')->first();
        $kolams = Kolam::all();
        
        return view('dashboard', compact('latest', 'kolams'));
    }

    public function chartData(Request $request)
    {
        $metric = $request->get('metric', 'ph');
        $kolamId = $request->get('kolam_id');
        $date = $request->get('date', now()->toDateString());
        $hour = $request->get('hour');

        $query = Monitoring::whereDate('waktu_monitoring', $date);

        if ($kolamId) {
            $query->where('kolam_id', $kolamId);
        }

        if ($hour !== null && $hour !== '') {
            // Detail data for specific hour
            $data = $query->whereHour('waktu_monitoring', $hour)
                ->orderBy('waktu_monitoring', 'asc')
                ->get(['waktu_monitoring', $metric]);
            
            $labels = $data->map(fn($item) => Carbon::parse($item->waktu_monitoring)->format('H:i'));
            $values = $data->pluck($metric);
        } else {
            // Aggregate hourly data
            $data = $query->selectRaw("HOUR(waktu_monitoring) as hour, AVG($metric) as avg_value")
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();
            
            $labels = $data->map(fn($item) => sprintf('%02d:00', $item->hour));
            $values = $data->pluck('avg_value');
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'metric' => $metric
        ]);
    }

    public function latestData()
    {
        $latest = Monitoring::with('kolam')->latest('waktu_monitoring')->first();
        
        return response()->json($latest);
    }
}

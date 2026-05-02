<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Monitoring;
use App\Models\Kolam;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringExport;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Monitoring::with('kolam.threshold')->latest('waktu_monitoring');

        if ($request->filled('start_date')) {
            $query->whereDate('waktu_monitoring', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('waktu_monitoring', '<=', $request->end_date);
        }

        if ($request->filled('kolam_id')) {
            $query->where('kolam_id', $request->kolam_id);
        }

        $monitorings = $query->paginate(10)->withQueryString();
        $kolams = Kolam::all();

        return view('monitoring.index', compact('monitorings', 'kolams'));
    }

    public function export(Request $request)
    {
        return Excel::download(new MonitoringExport($request), 'data_monitoring.xlsx');
    }
}

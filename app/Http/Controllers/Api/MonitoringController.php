<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\MonitoringRequest;
use App\Models\Monitoring;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function store(MonitoringRequest $request)
    {
        $validated = $request->validated();
        
        $deviceTime = Carbon::parse($validated['device_timestamp']);
        $now = now();
        
        // Calculate delay in milliseconds (absolute value)
        $delay = abs($now->diffInMilliseconds($deviceTime));
        
        $monitoring = Monitoring::create(array_merge($validated, [
            'device_timestamp' => $deviceTime->toDateTimeString(),
            'delay' => $delay,
            'waktu_monitoring' => $now
        ]));
        
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $monitoring
        ], 201);
    }
}

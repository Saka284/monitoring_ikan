<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Threshold;
use App\Models\Kolam;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        $kolams = Kolam::all();
        $selectedKolamId = $request->get('kolam_id', $kolams->first()?->id);
        $threshold = Threshold::where('kolam_id', $selectedKolamId)->first();

        return view('controlling.index', compact('kolams', 'threshold', 'selectedKolamId'));
    }

    public function update(Request $request, $kolamId)
    {
        $validated = $request->validate([
            'ph_bawah' => 'required|numeric|lt:ph_atas',
            'ph_atas' => 'required|numeric',
            'ketinggian_batas_bawah' => 'required|numeric|lt:ketinggian_batas_atas',
            'ketinggian_batas_atas' => 'required|numeric',
            'suhu_bawah' => 'required|numeric|lt:suhu_atas',
            'suhu_atas' => 'required|numeric',
            'salinitas_bawah' => 'required|numeric|lt:salinitas_atas',
            'salinitas_atas' => 'required|numeric',
        ]);

        Threshold::updateOrCreate(
            ['kolam_id' => $kolamId],
            $validated
        );

        return redirect()->back()->with('success', 'Threshold berhasil diperbarui');
    }
}

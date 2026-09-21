<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\StationResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StationController extends Controller
{
    protected $allowedStations = [
        'urin' => [
            'key' => 'urin',
            'title' => 'Stasiun 1: Tes Urin (Laboratorium)',
            'desc' => 'Uji parameter urin lengkap, glukosa, protein, dan sedimen.',
            'default_examiner' => 'dr. Hendra Sp.PK',
            'preset_notes' => 'Reduksi (-), Albumin (-), Sedimen dalam batas normal. Hasil laboratorium steril.',
        ],
        'napza' => [
            'key' => 'napza',
            'title' => 'Stasiun 2: Tes Skrining Bebas NAPZA',
            'desc' => 'Skrining 6 parameter narkotika: AMP, MET, THC, MOP, COC, BZO.',
            'default_examiner' => 'dr. Nurul Aini',
            'preset_notes' => 'Skrining 6 Parameter NAPZA: AMP (-), MET (-), THC (-), MOP (-), COC (-), BZO (-). Negatif.',
        ],
        'pemeriksaan' => [
            'key' => 'pemeriksaan',
            'title' => 'Stasiun 3: Pemeriksaan Fisik & Dokter',
            'desc' => 'Tanda-tanda vital, visus mata, buta warna, dan konfirmasi riwayat kesehatan.',
            'default_examiner' => 'dr. Nurul Aini',
            'preset_notes' => 'Tensi 120/80 mmHg, Nadi 80x/m, Visus ODS 6/6, Buta warna (-). Kondisi fisik layak dan sehat.',
        ],
    ];

    public function index(Request $request, string $station_name)
    {
        if (!array_key_exists($station_name, $this->allowedStations)) {
            return redirect()->route('staff.station.index', 'urin');
        }

        $stationInfo = $this->allowedStations[$station_name];

        // Students who are checked-in or in progress or completed
        $search = $request->get('search');
        $statusFilter = $request->get('status');

        $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'stationResults'])
            ->whereIn('status', ['checked_in', 'in_progress', 'completed']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('queue_code', 'like', "%{$search}%")
                    ->orWhereHas('user.profile', function ($pq) use ($search) {
                        $pq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%")
                            ->orWhere('faculty', 'like', "%{$search}%");
                    });
            });
        }

        $registrations = $query->orderBy('queue_code')->paginate(20)->withQueryString();

        return view('staff.stations.index', compact('stationInfo', 'station_name', 'registrations', 'search'));
    }

    public function storeResult(Request $request, string $station_name, Registration $registration)
    {
        if (!array_key_exists($station_name, $this->allowedStations)) {
            return back()->with('error', 'Stasiun pemeriksaan tidak valid.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pass,followup,fail'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'examiner_name' => ['required', 'string', 'max:255'],
        ]);

        StationResult::updateOrCreate(
            [
                'registration_id' => $registration->id,
                'station_name' => $station_name,
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'examiner_name' => $validated['examiner_name'],
                'examined_at' => Carbon::now(),
            ]
        );

        // Check if all 3 stations are now passed
        $freshRegistration = $registration->fresh(['stationResults']);
        if ($freshRegistration->isAllStationsPassed()) {
            $freshRegistration->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);
            $msg = "Hasil pemeriksaan stasiun berhasil disimpan! Seluruh stasiun telah berstatus 'PASS'. Status peserta otomatis menjadi 'COMPLETED' dan sertifikat siap diunduh.";
        } else {
            // Keep in progress
            if ($freshRegistration->status === 'checked_in') {
                $freshRegistration->update(['status' => 'in_progress']);
            }
            $msg = "Hasil pemeriksaan {$this->allowedStations[$station_name]['title']} untuk {$registration->user->name} berhasil disimpan.";
        }

        return back()->with('success', $msg);
    }
}

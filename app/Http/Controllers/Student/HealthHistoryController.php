<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HealthHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $health = $user->healthHistory;

        return view('student.health_history', compact('user', 'health'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'blood_type' => ['required', 'string', 'in:A,B,AB,O,Tidak Tahu'],
            'rhesus' => ['required', 'string', 'in:+,-,Tidak Tahu'],
            'height_cm' => ['required', 'numeric', 'min:50', 'max:250'],
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:300'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'chronic_diseases' => ['nullable', 'string', 'max:500'],
            'current_medications' => ['nullable', 'string', 'max:500'],
            'past_surgeries' => ['nullable', 'string', 'max:500'],
            'is_smoker' => ['required', 'boolean'],
            'vaccine_status' => ['required', 'string', 'max:100'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
        ]);

        HealthHistory::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('student.schedule')->with('success', 'Riwayat kesehatan berhasil disimpan! Silakan pilih sesi jadwal skrining.');
    }
}

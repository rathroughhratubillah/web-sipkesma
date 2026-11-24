<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\TestSession;
use Illuminate\Http\Request;

class TestSessionController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', 'all');
        $availableYears = Registration::getAvailableYears();

        $query = TestSession::withCount(['registrations' => function ($query) {
            $query->whereIn('status', ['awaiting_payment', 'awaiting_verification', 'cleared', 'checked_in', 'in_progress', 'completed']);
        }]);

        if ($selectedYear && $selectedYear !== 'all') {
            $query->where('academic_year', $selectedYear);
        }

        $sessions = $query
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time')
            ->get();

        return view('staff.sessions.index', compact('sessions', 'selectedYear', 'availableYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'session_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'quota' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        TestSession::create($validated);

        return back()->with('success', 'Sesi jadwal skrining berhasil ditambahkan.');
    }

    public function update(Request $request, TestSession $session)
    {
        $validated = $request->validate([
            'session_date' => ['required', 'date'],
            'session_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'quota' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $session->update($validated);

        return back()->with('success', 'Sesi jadwal skrining berhasil diperbarui.');
    }

    public function destroy(TestSession $session)
    {
        if ($session->registrations()->exists()) {
            return back()->with('error', 'Sesi ini tidak dapat dihapus karena sudah memiliki data pendaftaran peserta.');
        }

        $session->delete();

        return back()->with('success', 'Sesi jadwal skrining berhasil dihapus.');
    }
}

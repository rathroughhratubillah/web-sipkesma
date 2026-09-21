<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $registration = $user->registration ?? Registration::firstOrCreate(['user_id' => $user->id]);

        $sessions = TestSession::where('is_active', true)
            ->withCount(['registrations' => function ($query) {
                $query->whereIn('status', [
                    'awaiting_payment',
                    'awaiting_verification',
                    'cleared',
                    'checked_in',
                    'in_progress',
                    'completed'
                ]);
            }])
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        return view('student.schedule', compact('user', 'registration', 'sessions'));
    }

    public function select(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'test_session_id' => ['required', 'exists:test_sessions,id'],
        ]);

        $session = TestSession::findOrFail($validated['test_session_id']);

        if ($session->remaining_quota <= 0) {
            return back()->with('error', 'Maaf, kuota untuk sesi tersebut sudah penuh. Silakan pilih sesi lainnya.');
        }

        $registration = Registration::firstOrCreate(['user_id' => $user->id]);
        $registration->update([
            'test_session_id' => $session->id,
        ]);

        return redirect()->route('student.confirmation')->with('success', 'Jadwal sesi berhasil dipilih! Silakan tinjau dan konfirmasi data Anda.');
    }
}

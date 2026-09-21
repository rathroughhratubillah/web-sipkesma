<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfirmationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession']);

        if (!$user->profile) {
            return redirect()->route('student.profile')->with('warning', 'Harap lengkapi Data Diri terlebih dahulu.');
        }

        if (!$user->healthHistory) {
            return redirect()->route('student.health_history')->with('warning', 'Harap lengkapi Riwayat Kesehatan terlebih dahulu.');
        }

        $registration = $user->registration;
        if (!$registration || !$registration->test_session_id) {
            return redirect()->route('student.schedule')->with('warning', 'Harap pilih Sesi Jadwal skrining terlebih dahulu.');
        }

        return view('student.confirmation', compact('user', 'registration'));
    }

    public function lock(Request $request)
    {
        $user = Auth::user();
        $registration = $user->registration;

        if (!$registration || !$registration->test_session_id) {
            return redirect()->route('student.schedule')->with('error', 'Sesi belum dipilih.');
        }

        if ($registration->status === 'draft') {
            $registration->update([
                'status' => 'awaiting_payment',
            ]);
        }

        return redirect()->route('student.payment')->with('success', 'Data pendaftaran telah dikonfirmasi! Silakan lakukan pembayaran skrining.');
    }
}

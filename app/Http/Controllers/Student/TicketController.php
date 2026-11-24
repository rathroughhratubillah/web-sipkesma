<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'registration.testSession', 'registration.payment', 'healthHistory']);
        $registration = $user->registration;

        // Tiket hanya terbuka jika pembayaran sudah diverifikasi oleh admin (status: cleared, checked_in, in_progress, completed)
        $isUnlocked = $registration && in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']) && !empty($registration->queue_code);

        return view('student.ticket', compact('user', 'registration', 'isUnlocked'));
    }

    public function downloadPdf()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession', 'registration.payment']);
        $registration = $user->registration;

        if (!$registration || !in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']) || empty($registration->queue_code)) {
            return redirect()->route('student.ticket')->with('error', 'Tiket antrean belum tersedia. Menunggu verifikasi pembayaran oleh admin.');
        }

        $pdf = Pdf::loadView('tickets.pdf', compact('user', 'registration'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream('Tiket-Antrean-' . ($registration->queue_code ?? 'SIPKESMA') . '.pdf');
    }
}

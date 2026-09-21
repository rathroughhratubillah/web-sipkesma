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
        $user->load(['profile', 'registration.testSession', 'registration.payment']);
        $registration = $user->registration;

        $isUnlocked = $registration && in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']);

        $qrSvg = null;
        if ($isUnlocked) {
            try {
                $payload = $registration->qr_code_payload;
                $qrSvg = QrCode::size(200)->generate($payload);
            } catch (\Throwable $e) {
                $qrSvg = null;
            }
        }

        return view('student.ticket', compact('user', 'registration', 'isUnlocked', 'qrSvg'));
    }

    public function downloadPdf()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession']);
        $registration = $user->registration;

        if (!$registration || !in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed'])) {
            return redirect()->route('student.ticket')->with('error', 'Tiket antrean belum tersedia.');
        }

        $qrBase64 = null;
        try {
            $payload = $registration->qr_code_payload;
            $qrSvg = QrCode::format('svg')->size(160)->generate($payload);
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        } catch (\Throwable $e) {
            $qrBase64 = null;
        }

        $pdf = Pdf::loadView('tickets.pdf', compact('user', 'registration', 'qrBase64'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream('Tiket-Antrean-' . ($registration->queue_code ?? 'SIPKESMA') . '.pdf');
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\BarcodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(BarcodeService $barcode)
    {
        $user = Auth::user();
        $user->load(['profile', 'registration.testSession', 'registration.payment', 'healthHistory']);
        $registration = $user->registration;

        // Tiket hanya terbuka jika pembayaran sudah diverifikasi oleh admin (status: cleared, checked_in, in_progress, completed)
        $isUnlocked = $registration && in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']) && !empty($registration->queue_code);

        $barcodeCode = $isUnlocked ? $registration->ticket_barcode_code : null;
        $barcodeSvg = $isUnlocked ? $barcode->toSvg($barcodeCode, 54, 2) : null;

        return view('student.ticket', compact('user', 'registration', 'isUnlocked', 'barcodeCode', 'barcodeSvg'));
    }

    public function downloadPdf(BarcodeService $barcode)
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession', 'registration.payment']);
        $registration = $user->registration;

        if (!$registration || !in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']) || empty($registration->queue_code)) {
            return redirect()->route('student.ticket')->with('error', 'Tiket antrean belum tersedia. Menunggu verifikasi pembayaran oleh admin.');
        }

        $barcodeCode = $registration->ticket_barcode_code;
        $barcodeHtml = $barcode->toHtmlTable($barcodeCode, 38, 1);

        $pdf = Pdf::loadView('tickets.pdf', compact('user', 'registration', 'barcodeCode', 'barcodeHtml'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream('Tiket-Antrean-' . ($registration->queue_code ?? 'SIPKESMA') . '.pdf');
    }
}

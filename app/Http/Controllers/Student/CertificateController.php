<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession', 'registration.stationResults']);
        $registration = $user->registration;

        $isEligible = $registration && $registration->status === 'completed' && $registration->isAllStationsPassed();

        return view('student.certificate', compact('user', 'registration', 'isEligible'));
    }

    public function download()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession', 'registration.stationResults']);
        $registration = $user->registration;

        if (!$registration || $registration->status !== 'completed' || !$registration->isAllStationsPassed()) {
            return redirect()->route('student.certificate')->with('error', 'Sertifikat belum dapat diunduh karena proses skrining belum selesai atau belum dinyatakan lulus.');
        }

        $certificateNo = 'SIPKESMA/SKK/' . date('Y') . '/' . str_pad($registration->id, 5, '0', STR_PAD_LEFT);

        $validationPayload = route('certificate.verify', ['code' => $certificateNo]);
        $qrBase64 = null;
        try {
            $qrSvg = QrCode::format('svg')->size(140)->generate($validationPayload);
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        } catch (\Throwable $e) {
            $qrBase64 = null;
        }

        $pdf = Pdf::loadView('certificates.pdf', compact('user', 'registration', 'certificateNo', 'qrBase64'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Sertifikat-Kesehatan-' . ($user->profile->nim ?? 'MHS') . '.pdf');
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'registration.testSession', 'registration.payment']);
        $registration = $user->registration;

        if (!$registration || $registration->status === 'draft') {
            return redirect()->route('student.confirmation')->with('warning', 'Silakan konfirmasi pendaftaran terlebih dahulu.');
        }

        $payment = $registration->payment;

        // 2 Metode Pembayaran Resmi Sesuai Permintaan: SeaBank & DANA
        $seabankAccount = [
            'bank' => 'SeaBank',
            'number' => '901028036540',
            'name' => 'SIPKESMA KLINIK KESEHATAN',
        ];

        $danaAccount = [
            'bank' => 'DANA',
            'number' => '0882000260420',
            'name' => 'SIPKESMA',
        ];

        $session = $registration->testSession;
        $sessionDate = $session ? Carbon::parse($session->session_date)->locale('id')->isoFormat('D MMMM Y') : '15 Juni 2026';
        $sessionTime = $session ? $session->time_range : '08:00 - 10:00 WIB';

        // Generate QRIS / DANA Dynamic QR Code
        $danaQrPayload = '00020101021226680016ID.DANA.SIPKESMA011893600998088200026042052045812530336054061500005802ID5908SIPKESMA6010PALEMBANG61053011162280120SIPKESMA-REG-' . $registration->id . '6304A8F2';

        $danaQrSvg = null;
        try {
            $danaQrSvg = QrCode::size(230)->generate($danaQrPayload);
        } catch (\Throwable $e) {
            $danaQrSvg = null;
        }

        $amount = 150000;

        return view('student.payment', compact(
            'user',
            'registration',
            'payment',
            'seabankAccount',
            'danaAccount',
            'sessionDate',
            'sessionTime',
            'danaQrSvg',
            'danaQrPayload',
            'amount'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $registration = $user->registration;

        if (!$registration) {
            return redirect()->route('student.beranda')->with('error', 'Pendaftaran tidak valid.');
        }

        $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'bank_name' => ['required', 'string', 'max:100'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $file = $request->file('proof_file');
        $fileName = 'proof_' . $registration->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/proofs');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $fileName);
        $filePath = 'uploads/proofs/' . $fileName;

        // Transaksi dengan Pessimistic Locking untuk keamanan data
        DB::transaction(function () use ($registration, $filePath, $request, $user) {
            $reg = Registration::where('id', $registration->id)->lockForUpdate()->first();

            // Status registrasi TIDAK diubah — menunggu verifikasi admin
            // Queue code TIDAK digenerate — akan digenerate saat admin menyetujui

            Payment::updateOrCreate(
                ['registration_id' => $reg->id],
                [
                    'amount' => 150000,
                    'proof_file_path' => $filePath,
                    'bank_name' => $request->input('bank_name'),
                    'sender_name' => $request->input('sender_name'),
                    'status' => 'pending',
                    'verified_at' => null,
                    'rejection_reason' => null,
                ]
            );

            // Simpan nomor telepon jika ada pembaruan
            if ($request->filled('sender_phone') && $user->profile) {
                $user->profile->update(['phone' => $request->input('sender_phone')]);
            }
        });

        return redirect()->route('student.payment')->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari admin. Tiket akan aktif setelah pembayaran dikonfirmasi.');
    }
}

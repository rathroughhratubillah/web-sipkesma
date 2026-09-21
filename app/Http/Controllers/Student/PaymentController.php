<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $registration = $user->registration;

        if (!$registration || $registration->status === 'draft') {
            return redirect()->route('student.confirmation')->with('warning', 'Silakan konfirmasi pendaftaran terlebih dahulu.');
        }

        $payment = $registration->payment;

        $bankAccounts = [
            [
                'bank' => 'Bank Syariah Indonesia (BSI)',
                'number' => '7188-2938-1920',
                'name' => 'Klinik Sipkesma PPKMB UINSSC',
                'color' => 'teal',
            ],
            [
                'bank' => 'Bank Mandiri',
                'number' => '113-00-1928374-1',
                'name' => 'Klinik Sipkesma PPKMB UINSSC',
                'color' => 'blue',
            ],
            [
                'bank' => 'Bank BNI',
                'number' => '0829-1827-36',
                'name' => 'Klinik Sipkesma PPKMB UINSSC',
                'color' => 'emerald',
            ],
        ];

        return view('student.payment', compact('user', 'registration', 'payment', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $registration = $user->registration;

        if (!$registration) {
            return redirect()->route('student.beranda')->with('error', 'Pendaftaran tidak valid.');
        }

        $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            'bank_name' => ['required', 'string', 'max:100'],
            'sender_name' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('proof_file');
        $fileName = 'proof_' . $registration->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/proofs');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $fileName);
        $filePath = 'uploads/proofs/' . $fileName;

        Payment::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'amount' => 150000,
                'proof_file_path' => $filePath,
                'bank_name' => $request->input('bank_name'),
                'sender_name' => $request->input('sender_name'),
                'status' => 'pending',
                'rejection_reason' => null,
            ]
        );

        $registration->update([
            'status' => 'awaiting_verification',
        ]);

        return redirect()->route('student.payment')->with('success', 'Bukti pembayaran berhasil diunggah! Mohon menunggu verifikasi dari panitia medis.');
    }
}

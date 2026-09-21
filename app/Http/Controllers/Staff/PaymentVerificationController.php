<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = Payment::with(['registration.user.profile', 'registration.testSession', 'verifier']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'pending' => Payment::where('status', 'pending')->count(),
            'verified' => Payment::where('status', 'verified')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
        ];

        return view('staff.payments.index', compact('payments', 'status', 'counts'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status === 'verified') {
            return back()->with('info', 'Pembayaran ini sudah diverifikasi sebelumnya.');
        }

        DB::transaction(function () use ($payment) {
            $registration = $payment->registration()->lockForUpdate()->first();
            $session = $registration->testSession;

            // Generate sequential queue code per test session (e.g., A-001, A-002)
            $existingQueueCodes = Registration::where('test_session_id', $session?->id)
                ->whereNotNull('queue_code')
                ->lockForUpdate()
                ->pluck('queue_code')
                ->toArray();

            $maxNumber = 0;
            foreach ($existingQueueCodes as $code) {
                if (preg_match('/^[A-Z]-(\d+)$/', $code, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $maxNumber) {
                        $maxNumber = $num;
                    }
                }
            }

            $nextNumber = $maxNumber + 1;
            $queueCode = 'A-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Update Payment
            $payment->update([
                'status' => 'verified',
                'verified_at' => Carbon::now(),
                'verified_by' => Auth::id(),
                'rejection_reason' => null,
            ]);

            // Update Registration
            $registration->update([
                'status' => 'cleared',
                'queue_code' => $queueCode,
            ]);
        });

        return back()->with('success', "Pembayaran berhasil disetujui! Nomor antrean {$payment->registration->fresh()->queue_code} telah diterbitkan untuk peserta.");
    }

    public function reject(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'verified_at' => Carbon::now(),
                'verified_by' => Auth::id(),
            ]);

            $payment->registration->update([
                'status' => 'awaiting_payment',
            ]);
        });

        return back()->with('warning', 'Pembayaran ditolak dengan alasan: ' . $validated['rejection_reason']);
    }
}

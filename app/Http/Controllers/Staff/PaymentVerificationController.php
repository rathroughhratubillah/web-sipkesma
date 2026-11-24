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
        $method = $request->get('method', 'all');
        $selectedYear = $request->get('year', 'all');
        $availableYears = Registration::getAvailableYears();

        $query = Payment::with(['registration.user.profile', 'registration.testSession', 'verifier']);

        if ($selectedYear && $selectedYear !== 'all') {
            $query->whereHas('registration', fn($q) => $q->where('academic_year', $selectedYear));
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($method !== 'all') {
            $query->where('bank_name', 'like', "%{$method}%");
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $baseCountQuery = Payment::query();
        if ($selectedYear && $selectedYear !== 'all') {
            $baseCountQuery->whereHas('registration', fn($q) => $q->where('academic_year', $selectedYear));
        }

        $counts = [
            'pending' => (clone $baseCountQuery)->where('status', 'pending')->count(),
            'verified' => (clone $baseCountQuery)->where('status', 'verified')->count(),
            'rejected' => (clone $baseCountQuery)->where('status', 'rejected')->count(),
            'seabank' => (clone $baseCountQuery)->where('bank_name', 'like', '%SeaBank%')->count(),
            'dana' => (clone $baseCountQuery)->where('bank_name', 'like', '%DANA%')->count(),
        ];

        return view('staff.payments.index', compact('payments', 'status', 'method', 'counts', 'selectedYear', 'availableYears'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status === 'verified') {
            return back()->with('info', 'Pembayaran ini sudah diverifikasi sebelumnya.');
        }

        DB::transaction(function () use ($payment) {
            $registration = $payment->registration()->lockForUpdate()->first();
            $session = $registration->testSession;

            // Generate sequential queue code safely if not yet assigned
            $queueCode = $registration->queue_code ?: Registration::generateNextQueueCode($session?->id);

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

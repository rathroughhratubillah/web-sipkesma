<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index()
    {
        $todayCheckins = Registration::with(['user.profile', 'testSession'])
            ->whereIn('status', ['checked_in', 'in_progress', 'completed'])
            ->whereDate('checked_in_at', Carbon::today())
            ->latest('checked_in_at')
            ->take(15)
            ->get();

        $stats = [
            'total_checked_in_today' => $todayCheckins->count(),
            'ready_to_checkin' => Registration::where('status', 'cleared')->count(),
        ];

        return view('staff.checkin.index', compact('todayCheckins', 'stats'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string'],
        ]);

        $input = trim($request->input('query'));
        $registration = null;

        // Check if QR payload format SIPKESMA:id:queue:nim
        if (str_starts_with($input, 'SIPKESMA:')) {
            $parts = explode(':', $input);
            $regId = $parts[1] ?? null;
            if ($regId) {
                $registration = Registration::with('user.profile', 'testSession')->find($regId);
            }
        }

        // Search by queue code (e.g., A-001)
        if (!$registration) {
            $registration = Registration::with('user.profile', 'testSession')
                ->where('queue_code', strtoupper($input))
                ->first();
        }

        // Search by NIM
        if (!$registration) {
            $profile = Profile::where('nim', $input)->first();
            if ($profile) {
                $registration = Registration::with('user.profile', 'testSession')
                    ->where('user_id', $profile->user_id)
                    ->first();
            }
        }

        if (!$registration) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Data peserta dengan kata kunci '{$input}' tidak ditemukan.",
                ], 404);
            }
            return back()->with('error', "Peserta dengan kata kunci '{$input}' tidak ditemukan.");
        }

        if ($registration->status === 'draft' || $registration->status === 'awaiting_payment' || $registration->status === 'awaiting_verification') {
            $msg = "Peserta belum menyelesaikan verifikasi pembayaran. Status saat ini: {$registration->status}";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        if (in_array($registration->status, ['checked_in', 'in_progress', 'completed'])) {
            $msg = "Peserta {$registration->user->name} (No: {$registration->queue_code}) sudah melakukan check-in sebelumnya pada " . ($registration->checked_in_at?->format('H:i:s') ?? '-');
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'already_checked_in' => true, 'message' => $msg, 'registration' => $registration]);
            }
            return back()->with('info', $msg);
        }

        $registration->update([
            'status' => 'checked_in',
            'checked_in_at' => Carbon::now(),
        ]);

        $successMsg = "Check-in BERHASIL! Selamat datang {$registration->user->name} ({$registration->queue_code}). Silakan menuju ruang tunggu pemeriksaan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'registration' => $registration->load('user.profile'),
            ]);
        }

        return back()->with('success', $successMsg);
    }
}

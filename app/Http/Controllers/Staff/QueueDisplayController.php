<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\TestSession;
use Illuminate\Http\Request;

class QueueDisplayController extends Controller
{
    public function index(Request $request)
    {
        $selectedSessionId = $request->get('session_id');

        $sessions = TestSession::where('is_active', true)->orderBy('session_date')->get();

        if (!$selectedSessionId && $sessions->isNotEmpty()) {
            $selectedSessionId = $sessions->first()->id;
        }

        $waitingList = Registration::with('user.profile')
            ->where('test_session_id', $selectedSessionId)
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->orderBy('queue_code')
            ->get();

        $currentCalling = Registration::with('user.profile')
            ->where('test_session_id', $selectedSessionId)
            ->where('status', 'in_progress')
            ->latest('updated_at')
            ->first();

        $recentlyCompleted = Registration::with('user.profile')
            ->where('test_session_id', $selectedSessionId)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(5)
            ->get();

        return view('staff.queue.display', compact('sessions', 'selectedSessionId', 'waitingList', 'currentCalling', 'recentlyCompleted'));
    }

    public function call(Request $request, Registration $registration)
    {
        $destination = $request->input('destination', 'Ruang Pemeriksaan');

        $registration->update([
            'status' => 'in_progress',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Memanggil nomor antrean {$registration->queue_code}",
            'queue_code' => $registration->queue_code,
            'student_name' => $registration->user->name,
            'destination' => $destination,
        ]);
    }
}

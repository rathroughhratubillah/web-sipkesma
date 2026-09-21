<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\TestSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Registration::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'checked_in' => Registration::whereIn('status', ['checked_in', 'in_progress'])->count(),
            'completed' => Registration::where('status', 'completed')->count(),
            'cleared' => Registration::where('status', 'cleared')->count(),
        ];

        $recentRegistrations = Registration::with(['user.profile', 'testSession', 'payment'])
            ->latest('updated_at')
            ->take(8)
            ->get();

        $activeSessions = TestSession::where('is_active', true)
            ->withCount(['registrations' => function ($q) {
                $q->whereIn('status', ['awaiting_payment', 'awaiting_verification', 'cleared', 'checked_in', 'in_progress', 'completed']);
            }])
            ->get();

        return view('staff.dashboard', compact('stats', 'recentRegistrations', 'activeSessions'));
    }
}

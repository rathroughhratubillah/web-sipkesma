<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\TestSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('year', 'all');
        $availableYears = Registration::getAvailableYears();

        $regQuery = Registration::query();
        $payQuery = Payment::query();
        $sessionQuery = TestSession::where('is_active', true);

        if ($selectedYear && $selectedYear !== 'all') {
            $regQuery->where('academic_year', $selectedYear);
            $payQuery->whereHas('registration', fn($q) => $q->where('academic_year', $selectedYear));
            $sessionQuery->where('academic_year', $selectedYear);
        }

        $stats = [
            'total_students' => (clone $regQuery)->count(),
            'pending_payments' => (clone $payQuery)->where('status', 'pending')->count(),
            'checked_in' => (clone $regQuery)->whereIn('status', ['checked_in', 'in_progress'])->count(),
            'completed' => (clone $regQuery)->where('status', 'completed')->count(),
            'cleared' => (clone $regQuery)->where('status', 'cleared')->count(),
        ];

        $recentQuery = Registration::with(['user.profile', 'testSession', 'payment']);
        if ($selectedYear && $selectedYear !== 'all') {
            $recentQuery->where('academic_year', $selectedYear);
        }

        $recentRegistrations = $recentQuery
            ->latest('updated_at')
            ->take(8)
            ->get();

        $activeSessions = $sessionQuery
            ->withCount(['registrations' => function ($q) use ($selectedYear) {
                if ($selectedYear && $selectedYear !== 'all') {
                    $q->where('academic_year', $selectedYear);
                }
                $q->whereIn('status', ['awaiting_payment', 'awaiting_verification', 'cleared', 'checked_in', 'in_progress', 'completed']);
            }])
            ->get();

        return view('staff.dashboard', compact('stats', 'recentRegistrations', 'activeSessions', 'selectedYear', 'availableYears'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TestSession;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingSessions = TestSession::where('is_active', true)
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->take(4)
            ->get();

        return view('landing', compact('upcomingSessions'));
    }
}

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
        return redirect()->route('staff.station.index', 'urin');
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

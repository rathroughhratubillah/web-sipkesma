<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $code = $request->query('code');
        $regId = null;

        if ($code && preg_match('/SIPKESMA\/SKK\/\d+\/(\d+)/', $code, $matches)) {
            $regId = (int) $matches[1];
        }

        $registration = null;
        if ($regId) {
            $registration = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'stationResults'])
                ->where('id', $regId)
                ->where('status', 'completed')
                ->first();
        }

        return view('certificates.verify', compact('registration', 'code'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function show(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);
        return view('certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        abort_unless($certificate->user_id === Auth::id(), 403);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('STEMLY-Certificate-'.$certificate->certificate_number.'.pdf');
    }

    /** Public verification page (no auth). */
    public function verify(Request $request)
    {
        $certificate = null;
        $code = $request->string('code')->trim()->value();
        if ($code) {
            $certificate = Certificate::with(['user', 'course'])
                ->where('certificate_number', $code)->first();
        }
        return view('certificates.verify', compact('certificate', 'code'));
    }
}

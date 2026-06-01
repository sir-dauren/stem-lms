<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $available = array_keys(config('locales.available', []));

        if (in_array($locale, $available, true)) {
            session(['locale' => $locale]);

            if (Auth::check()) {
                Auth::user()->forceFill(['locale' => $locale])->save();
            }
        }

        return redirect()->back();
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('locales.available', []));
        $default   = config('app.locale');

        // Priority: session → authenticated user preference → app default.
        $locale = session('locale')
            ?? (Auth::check() ? Auth::user()->locale : null)
            ?? $default;

        if (! in_array($locale, $available, true)) {
            $locale = $default;
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}

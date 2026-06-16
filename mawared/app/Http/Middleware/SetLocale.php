<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('locales.default'));

        if (! in_array($locale, config('locales.supported'), true)) {
            $locale = config('locales.default');
        }

        App::setLocale($locale);

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, config('locales.supported'), true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        return redirect()->back(fallback: route('login'));
    }
}

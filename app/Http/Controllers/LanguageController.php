<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch($locale): RedirectResponse
    {
        // Validate the locale
        if (!in_array($locale, ["en", "pl"], true)) {
            return redirect()->back();
        }

        // Store the locale in session
        session()->put("locale", $locale);

        // Also store in cookie for persistence across sessions
        cookie()->queue("locale", $locale, 60 * 24 * 30); // 30 days

        // Set the application locale
        App::setLocale($locale);

        return redirect()->back();
    }
}

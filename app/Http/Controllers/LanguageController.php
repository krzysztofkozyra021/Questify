<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Validate the locale
        if (!in_array($locale, ["en", "pl"], true)) {
            return redirect()->back();
        }

        // Store the locale in session
        session()->put("locale", $locale);

        return redirect()->back();
    }
}

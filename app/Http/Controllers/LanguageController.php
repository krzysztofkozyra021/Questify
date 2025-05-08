<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        // Validate the locale
        if (!in_array($locale, ['en', 'pl'])) {
            return redirect()->back();
        }

        // Store the locale in session
        session()->put('locale', $locale);

        return redirect()->back();
    }
} 
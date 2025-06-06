<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): Response
    {
        $user = Auth::user()->load(["userStatistics", "userStatistics.classAttributes"]);
        $userStatistics = $user->userStatistics;
        $locales = config("app.available_locales", ["en"]);
        $currentLocale = app()->getLocale();
        $userClassName = $userStatistics->classAttributes->class_name;

        return Inertia::render("Settings", [
            "user" => $user,
            "userStatistics" => $userStatistics,
            "locales" => $locales,
            "currentLocale" => $currentLocale,
            "userClassName" => $userClassName,
        ]);
    }

    /**
     * Update the user's settings.
     */
    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Update user settings
        $user->update($request->validated());

        return redirect()->route("settings")->with("success", "Settings updated successfully.");
    }

    /**
     * Change user's locale
     */
    public function changeLocale(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("settings")->with("error", "User not found.");
        }

        $locale = $request->input("locale");
        $allowedLocales = config("app.available_locales", ["en", "pl", "de"]);

        // Validate the locale against allowed locales
        if (!in_array($locale, $allowedLocales, true)) {
            return redirect()->route("settings")->with("error", "Invalid locale selected.");
        }

        // Store the locale in the session
        session()->put("locale", $locale);

        // Set the application locale
        app()->setLocale($locale);

        return redirect()->route("settings")->with("success", "Locale changed successfully.");
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Share locale data with all Inertia views
        Inertia::share([
            "locale" => fn() => app()->getLocale(),
            "availableLocales" => fn() => config("app.available_locales"),
            // Share translations for the current page
            "translations" => function () {
                // Get current locale
                $locale = app()->getLocale();

                // Path to the JSON translation file
                $path = resource_path("lang/{$locale}.json");

                // Check if the file exists, otherwise use English
                if (!File::exists($path)) {
                    $path = resource_path("lang/en.json");
                }

                // If no translation file exists, return empty array
                if (!File::exists($path)) {
                    return [];
                }

                // Read and decode the JSON file
                $translations = json_decode(File::get($path), true) ?? [];

                // Also load English translations if current locale isn't English
                // This ensures we have fallbacks for missing translations
                if ($locale !== "en") {
                    $enPath = resource_path("lang/en.json");

                    if (File::exists($enPath)) {
                        $enTranslations = json_decode(File::get($enPath), true) ?? [];

                        // Only add English translations that don't exist in current locale
                        foreach ($enTranslations as $key => $value) {
                            if (!isset($translations[$key])) {
                                $translations[$key] = $value;
                            }
                        }
                    }
                }

                return $translations;
            },
        ]);
    }
}

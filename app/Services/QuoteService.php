<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QuoteService
{
    public function getQuote(string $locale): array
    {
        if (!config("app.enable_apis")) {
            Log::info("APIs are disabled in config");

            return [];
        }

        // Set API language based on locale - Russian or default to English
        $apiLang = match(strtolower(substr($locale, 0, 2))) {
            "ru" => "ru",
            default => "en",
        };

        $cacheKey = "quote_{$apiLang}";

        return Cache::remember($cacheKey, 3600, function () use ($apiLang) {
            try {
                // Call Forismatic API with correct endpoint and format
                $response = Http::asForm()->post("http://api.forismatic.com/api/1.0/", [
                    "method" => "getQuote",
                    "format" => "json",
                    "lang" => $apiLang,
                ]);

                if ($response->status() !== 200) {
                    Log::error("Forismatic API error", [
                        "status" => $response->status(),
                        "body" => $response->body(),
                    ]);

                    return [];
                }

                $quote = $response->json();

                if (!isset($quote["quoteText"])) {
                    Log::error("Invalid quote format", ["response" => $quote]);

                    return [];
                }

                return [
                    "text" => $quote["quoteText"],
                    "author" => $quote["quoteAuthor"] ?: "Unknown",
                    "lang" => $apiLang,
                ];
            } catch (\Exception $e) {
                Log::error("Error fetching quote", [
                    "error" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);

                return [];
            }
        });
    }
}

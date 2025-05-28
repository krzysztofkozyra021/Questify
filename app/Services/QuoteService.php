<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QuoteService
{
    public function getQuote(string $locale): array
    {
        if (!config('app.enable_apis')) {
            return [];
        }

        // Set API language based on locale - Russian or default to English
        $apiLang = match(strtolower(substr($locale, 0, 2))) {
            'ru' => 'ru',
            default => 'en'
        };

        // Call Forismatic API
        $response = Http::asForm()->post('http://api.forismatic.com/api/1.0/', [
            'method' => 'getQuote',
            'format' => 'json',
            'lang' => $apiLang
        ]);

        if ($response->status() !== 200) {
            return [];
        }

        $quote = $response->json();
        return [
            'text' => $quote['quoteText'],
            'author' => $quote['quoteAuthor'] ?: 'Unknown',
            'lang' => $apiLang
        ];
    }
} 
<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TranslationService
{
    public function translate(string $text, string $targetLang): ?string
    {
        if (!config("app.enable_apis")) {
            return null;
        }

        $cacheKey = "translation_" . md5($text . $targetLang);

        return Cache::remember($cacheKey, 86400, function () use ($text, $targetLang) {
            $deeplApiKey = config("services.deepl.api_key");
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "DeepL-Auth-Key " . $deeplApiKey,
            ])->post("https://api-free.deepl.com/v2/translate", [
                "text" => [$text],
                "target_lang" => $targetLang,
            ]);

            if ($response->status() !== 200) {
                return;
            }

            $result = $response->json();

            return $result["translations"][0]["text"] ?? null;
        });
    }
}

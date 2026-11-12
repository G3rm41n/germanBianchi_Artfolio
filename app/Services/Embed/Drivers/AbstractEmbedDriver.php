<?php

namespace App\Services\Embed\Drivers;

use App\Services\Embed\Contracts\EmbedDriverInterface;
use Illuminate\Support\Facades\Http;

abstract class AbstractEmbedDriver implements EmbedDriverInterface
{
    /**
     * Timeout estándar en segundos para peticiones oEmbed/scraping externos.
     */
    protected int $timeoutSeconds = 5;

    /**
     * Realiza una petición GET segura con User-Agent moderno y timeout controlado.
     */
    protected function safeHttpGet(string $url, array $headers = []): ?\Illuminate\Http\Client\Response
    {
        try {
            $defaultHeaders = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,application/json,*/*;q=0.8',
            ];

            $response = Http::withHeaders(array_merge($defaultHeaders, $headers))
                ->timeout($this->timeoutSeconds)
                ->get($url);

            return $response->successful() ? $response : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Limpia títulos eliminando sufijos comunes de branding web ("| DeviantArt", "- YouTube", etc.).
     */
    protected function cleanTitle(?string $title, array $suffixesToRemove = []): ?string
    {
        if (!$title) {
            return null;
        }

        $clean = html_entity_decode(trim($title), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        foreach ($suffixesToRemove as $suffix) {
            if (str_ends_with(strtolower($clean), strtolower($suffix))) {
                $clean = substr($clean, 0, -strlen($suffix));
            }
        }

        return trim($clean) ?: null;
    }

    /**
     * Extrae un meta tag específico de un documento HTML crudo.
     */
    protected function extractMetaTag(string $html, string $nameOrProperty): ?string
    {
        $escaped = preg_quote($nameOrProperty, '/');
        // Match <meta property="og:..." content="..."> or <meta name="..." content="...">
        if (preg_match('/<meta\s+[^>]*(?:property|name)=["\']' . $escaped . '["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Match invertido: <meta content="..." property="og:...">
        if (preg_match('/<meta\s+[^>]*content=["\']([^"\']+)["\'][^>]*(?:property|name)=["\']' . $escaped . '["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }
}


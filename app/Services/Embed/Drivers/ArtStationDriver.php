<?php

namespace App\Services\Embed\Drivers;

class ArtStationDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'artstation';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'artstation.com' || str_ends_with($host, '.artstation.com');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;

        // ArtStation no tiene oEmbed público pero sí provee OpenGraph completo y Twitter Cards
        $pageRes = $this->safeHttpGet($url);
        if ($pageRes) {
            $html = $pageRes->body();
            $title = $this->extractMetaTag($html, 'og:title');
            $imageUrl = $this->extractMetaTag($html, 'og:image');

            // Fallback para twitter:image
            if (!$imageUrl) {
                $imageUrl = $this->extractMetaTag($html, 'twitter:image');
            }
        }

        // Limpiar sufijos de ArtStation
        $title = $this->cleanTitle($title, ['- ArtStation', '| ArtStation', 'on ArtStation']);

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'image',
            'render_type'   => 'image',
            'embed_html'    => null,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}


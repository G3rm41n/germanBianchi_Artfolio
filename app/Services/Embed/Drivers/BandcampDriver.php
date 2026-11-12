<?php

namespace App\Services\Embed\Drivers;

class BandcampDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'bandcamp';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'bandcamp.com' || str_ends_with($host, '.bandcamp.com');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // Bandcamp provee metadatos mediante OpenGraph tags
        $pageRes = $this->safeHttpGet($url);
        if ($pageRes) {
            $html = $pageRes->body();
            $title = $this->extractMetaTag($html, 'og:title');
            $imageUrl = $this->extractMetaTag($html, 'og:image');

            // Extraer ID del álbum o track si está disponible en meta tags de twitter/player
            $playerUrl = $this->extractMetaTag($html, 'twitter:player');
            if ($playerUrl) {
                $embedHtml = '<iframe style="border: 0; width: 100%; height: 120px;" src="' . $playerUrl . '" seamless></iframe>';
            }
        }

        // Limpiar sufijo "| Bandcamp"
        $title = $this->cleanTitle($title, ['| Bandcamp', '- Bandcamp']);

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'audio',
            'render_type'   => 'iframe',
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}


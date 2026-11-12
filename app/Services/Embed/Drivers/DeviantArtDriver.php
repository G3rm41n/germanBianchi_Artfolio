<?php

namespace App\Services\Embed\Drivers;

class DeviantArtDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'deviantart';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'deviantart.com' 
            || str_ends_with($host, '.deviantart.com') 
            || $host === 'sta.sh';
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;

        // 1. DeviantArt backend oEmbed oficial (público, sin auth, soporta CORS y formatos directos)
        $oembedUrl = 'https://backend.deviantart.com/oembed?url=' . urlencode($url);
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            // 'url' es la imagen a tamaño completo; si no existe, usar 'thumbnail_url'
            $imageUrl = $data['url'] ?? ($data['thumbnail_url'] ?? null);
        }

        // 2. Fallback: Scraping directo de meta tags en caso de que oEmbed falle
        if (!$imageUrl) {
            $pageRes = $this->safeHttpGet($url);
            if ($pageRes) {
                $html = $pageRes->body();

                if (!$title) {
                    $title = $this->extractMetaTag($html, 'og:title');
                }

                $imageUrl = $this->extractMetaTag($html, 'og:image');
            }
        }

        // Limpiar sufijo "| DeviantArt" del título
        $title = $this->cleanTitle($title, ['| DeviantArt', '- DeviantArt', 'on DeviantArt']);

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


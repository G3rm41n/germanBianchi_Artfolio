<?php

namespace App\Services\Embed\Drivers;

class SketchfabDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'sketchfab';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'sketchfab.com' || str_ends_with($host, '.sketchfab.com');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // 1. Extraer el UID del modelo (los últimos 32 caracteres hexadecimales de la URL)
        $modelId = null;
        if (preg_match('/([a-f0-9]{32})(?:[\/?#]|$)/i', $url, $m)) {
            $modelId = $m[1];
        }

        // Construir embed HTML estándar seguro
        if ($modelId) {
            $embedHtml = '<iframe title="Sketchfab 3D Model" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/' . $modelId . '/embed" class="w-full h-full aspect-video rounded-xl"></iframe>';
        }

        // 2. Sketchfab oEmbed oficial (devuelve miniatura y título del autor)
        $oembedUrl = 'https://sketchfab.com/oembed?url=' . urlencode($url);
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            $imageUrl = $data['thumbnail_url'] ?? null;
            if (!empty($data['html'])) {
                $embedHtml = $data['html'];
            }
        }

        // 3. Fallback: Scraping de meta tags og:image y og:title
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

        // Limpiar sufijo "- Download Free 3D model by ... - Sketchfab"
        $title = $this->cleanTitle($title, ['- Sketchfab', '| Sketchfab']);

        return [
            'provider'      => $this->getProviderName(),
            'category'      => '3d',
            'render_type'   => 'iframe',
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}


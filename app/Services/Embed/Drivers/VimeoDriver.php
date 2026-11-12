<?php

namespace App\Services\Embed\Drivers;

class VimeoDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'vimeo';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'vimeo.com' || str_ends_with($host, '.vimeo.com');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // Vimeo oEmbed oficial (público)
        $oembedUrl = 'https://vimeo.com/api/oembed.json?url=' . urlencode($url);
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            $imageUrl = $data['thumbnail_url'] ?? null;
            $embedHtml = $data['html'] ?? null;
        }

        // Fallback: Si no hay embed HTML, intentar extraer video ID
        if (!$embedHtml && preg_match('#vimeo\.com/(?:channels/(?:\w+/)?|groups/[^/]+/videos/|video/|)(\d+)#', $url, $m)) {
            $videoId = $m[1];
            $embedHtml = '<iframe src="https://player.vimeo.com/video/' . $videoId . '" class="w-full h-full aspect-video rounded-xl" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'video',
            'render_type'   => 'iframe',
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}


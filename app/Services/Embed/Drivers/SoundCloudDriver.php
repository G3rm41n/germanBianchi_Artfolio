<?php

namespace App\Services\Embed\Drivers;

class SoundCloudDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'soundcloud';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'soundcloud.com' || str_ends_with($host, '.soundcloud.com');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // SoundCloud oEmbed oficial
        $oembedUrl = 'https://soundcloud.com/oembed?url=' . urlencode($url) . '&format=json';
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            $imageUrl = $data['thumbnail_url'] ?? null;
            $embedHtml = $data['html'] ?? null;
        }

        // Fallback: Embed genérico de SoundCloud
        if (!$embedHtml) {
            $embedHtml = '<iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=' . urlencode($url) . '&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true"></iframe>';
        }

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


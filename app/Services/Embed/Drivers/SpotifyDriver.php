<?php

namespace App\Services\Embed\Drivers;

class SpotifyDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'spotify';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'spotify.com' 
            || str_ends_with($host, '.spotify.com') 
            || $host === 'open.spotify.com';
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // Construir URL del iframe embed de Spotify
        // Ej: open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT -> open.spotify.com/embed/track/4cOdK2wGLETKBW3PvgPWqT
        if (preg_match('#(?:open\.)?spotify\.com/(track|album|playlist|episode)/([a-zA-Z0-9]+)#i', $url, $m)) {
            $type = $m[1];
            $id = $m[2];
            $height = in_array($type, ['album', 'playlist']) ? '380' : '152';
            $embedHtml = '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/' . $type . '/' . $id . '?utm_source=generator" width="100%" height="' . $height . '" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
        }

        // Spotify oEmbed oficial
        $oembedUrl = 'https://open.spotify.com/oembed?url=' . urlencode($url);
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            $imageUrl = $data['thumbnail_url'] ?? null;
            if (!$embedHtml && !empty($data['html'])) {
                $embedHtml = $data['html'];
            }
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


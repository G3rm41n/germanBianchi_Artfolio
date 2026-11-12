<?php

namespace App\Services\Embed\Drivers;

class YouTubeDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'youtube';
    }

    public function matches(string $host, string $url): bool
    {
        return in_array($host, ['youtube.com', 'youtu.be', 'm.youtube.com']) 
            || str_ends_with($host, '.youtube.com');
    }

    public function process(string $url, string $host): ?array
    {
        $videoId = $this->extractVideoId($url);
        $title = null;
        $imageUrl = null;
        $embedHtml = null;

        if ($videoId) {
            $imageUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            $embedHtml = '<iframe src="https://www.youtube.com/embed/' . $videoId . '" class="w-full h-full aspect-video rounded-xl" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';

            // Consultar oEmbed oficial de YouTube para obtener el título real del video
            $oembedUrl = "https://www.youtube.com/oembed?url=" . urlencode($url) . "&format=json";
            $res = $this->safeHttpGet($oembedUrl);
            if ($res && $res->json()) {
                $data = $res->json();
                $title = $data['title'] ?? null;
                // Si maxresdefault no estuviera disponible, YouTube oEmbed provee thumbnail_url
                if (!empty($data['thumbnail_url'])) {
                    $imageUrl = $data['thumbnail_url'];
                }
            }
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

    protected function extractVideoId(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match)) {
            return $match[1];
        }

        // Shorts format: youtube.com/shorts/{id}
        if (preg_match('/youtube\.com\/shorts\/([^"&?\/ ]{11})/i', $url, $match)) {
            return $match[1];
        }

        return null;
    }
}


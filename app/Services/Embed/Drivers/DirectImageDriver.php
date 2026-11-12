<?php

namespace App\Services\Embed\Drivers;

class DirectImageDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'image_direct';
    }

    public function matches(string $host, string $url): bool
    {
        return str_ends_with($host, 'pinimg.com') 
            || in_array($host, ['imgur.com', 'i.imgur.com']);
    }

    public function process(string $url, string $host): ?array
    {
        $thumbnailUrl = $url;
        if (str_contains($host, 'pinimg.com')) {
            $thumbnailUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $url);
        }

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'image',
            'render_type'   => 'image',
            'embed_html'    => null,
            'thumbnail_url' => $thumbnailUrl,
            'title'         => 'Direct Image Artwork',
        ];
    }
}

<?php

namespace App\Services\Embed\Drivers;

class CodePenDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'codepen';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'codepen.io' || str_ends_with($host, '.codepen.io');
    }

    public function process(string $url, string $host): ?array
    {
        $imageUrl = null;
        $title = null;
        $embedHtml = null;

        // CodePen oEmbed oficial
        $oembedUrl = 'https://codepen.io/api/oembed?url=' . urlencode($url) . '&format=json';
        $res = $this->safeHttpGet($oembedUrl);

        if ($res && $res->json()) {
            $data = $res->json();
            $title = $data['title'] ?? null;
            $imageUrl = $data['thumbnail_url'] ?? null;
            $embedHtml = $data['html'] ?? null;
        }

        // Fallback para pens: codepen.io/{user}/pen/{slug}
        if (!$embedHtml && preg_match('#codepen\.io/([^/]+)/pen/([^/?\#]+)#i', $url, $m)) {
            $user = $m[1];
            $slug = $m[2];
            $embedHtml = '<iframe height="400" style="width: 100%;" scrolling="no" title="' . ($title ?: 'CodePen Embed') . '" src="https://codepen.io/' . $user . '/embed/' . $slug . '?default-tab=result" frameborder="no" loading="lazy" allowtransparency="true" allowfullscreen="true"></iframe>';
        }

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'doc',
            'render_type'   => 'iframe',
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}

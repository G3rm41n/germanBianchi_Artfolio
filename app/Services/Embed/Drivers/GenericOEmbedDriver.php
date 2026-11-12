<?php

namespace App\Services\Embed\Drivers;

class GenericOEmbedDriver extends AbstractEmbedDriver
{
    protected string $providerName;
    protected string $oembedEndpoint;
    protected array $domains;
    protected string $category;
    protected string $renderType;

    public function __construct(
        string $providerName,
        string $oembedEndpoint,
        array $domains,
        string $category = 'image',
        string $renderType = 'image'
    ) {
        $this->providerName = $providerName;
        $this->oembedEndpoint = $oembedEndpoint;
        $this->domains = $domains;
        $this->category = $category;
        $this->renderType = $renderType;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function matches(string $host, string $url): bool
    {
        foreach ($this->domains as $domain) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }
        return false;
    }

    public function process(string $url, string $host): ?array
    {
        $endpoint = str_contains($this->oembedEndpoint, '%s')
            ? sprintf($this->oembedEndpoint, urlencode($url))
            : $this->oembedEndpoint . '?url=' . urlencode($url);

        $response = $this->safeHttpGet($endpoint);

        $title = null;
        $thumbnailUrl = null;
        $embedHtml = null;

        if ($response && $response->json()) {
            $data = $response->json();
            $title = $data['title'] ?? null;
            $thumbnailUrl = $data['thumbnail_url'] ?? ($data['url'] ?? null);
            $embedHtml = $data['html'] ?? null;
        }

        return [
            'provider'      => $this->providerName,
            'category'      => $this->category,
            'render_type'   => $this->renderType,
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $thumbnailUrl,
            'title'         => $title,
        ];
    }
}


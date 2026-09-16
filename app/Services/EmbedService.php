<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbedService
{
    /**
     * Lista blanca de dominios permitidos para incrustar obras.
     */
    protected array $allowedDomains = [
        'sketchfab.com',
        'artstation.com',
        'youtube.com',
        'youtu.be',
        'vimeo.com',
        'soundcloud.com',
        'spotify.com',
        'pinterest.com',
        'ar.pinterest.com',
        'es.pinterest.com',
        'pin.it',
        'i.pinimg.com', // Enlaces directos de imágenes de Pinterest
        'deviantart.com',
        'drive.google.com',
        'docs.google.com',
        'imgur.com',
        'i.imgur.com',
    ];

    /**
     * Valida y extrae la información básica de una URL.
     * Si la URL no es válida o no está permitida, retorna null.
     *
     * @param string $url La URL ingresada por el artista
     * @return array|null
     */
    public function processUrl(string $url): ?array
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['host'])) {
            return null;
        }

        $host = strtolower($parsedUrl['host']);

        // Limpiar el subdominio 'www.' para facilitar la validación
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        // 1. Validar contra la Lista Blanca (RN-01)
        if (!in_array($host, $this->allowedDomains)) {
            return null;
        }

        // 2. Seguridad: Prevención SSRF básica
        // Se descartan IPs directas y 'localhost' por si evaden el parsing (RNF-02)
        if (filter_var($host, FILTER_VALIDATE_IP) !== false || $host === 'localhost') {
            return null;
        }

        // 3. Determinar el Proveedor
        $provider = $this->determineProvider($host);

        // 4. Determinar la Categoría y el Tipo de Renderizado
        $category = $this->determineCategory($provider);
        $renderType = $this->determineRenderType($category);

        // 5. Enriquecimiento mediante oEmbed / metadatos externos según proveedor
        $thumbnailUrl = null;
        $embedHtml = null;

        if ($provider === 'pinterest') {
            [$embedHtml, $thumbnailUrl] = $this->fetchPinterestOEmbed($url);
            $renderType = 'iframe'; // Pinterest oEmbed siempre retorna un iframe
        } elseif ($provider === 'image_direct') {
            // Es un enlace directo a un archivo de imagen (i.pinimg.com, imgur.com, etc.)
            $thumbnailUrl = $url;
            // No se genera iframe; el visor lo renderizará como <img>
        } else {
            $embedHtml = $this->generateEmbedHtml($provider, $url);
        }

        return [
            'provider'      => $provider,
            'category'      => $category,
            'render_type'   => $renderType,
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $thumbnailUrl,
        ];
    }

    /**
     * Llama a la API oEmbed de Pinterest para obtener el iframe y la miniatura.
     * Retorna [embedHtml, thumbnailUrl]
     */
    protected function fetchPinterestOEmbed(string $url): array
    {
        try {
            $oembedEndpoint = 'https://www.pinterest.com/oembed.json';
            $response = Http::timeout(5)->get($oembedEndpoint, ['url' => $url]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    $data['html'] ?? null,
                    $data['thumbnail_url'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Si falla, continuar sin embed. El visor mostrará el enlace directo.
        }

        return [null, null];
    }

    /**
     * Retorna el identificador del proveedor en base al host.
     */
    protected function determineProvider(string $host): string
    {
        return match (true) {
            in_array($host, ['youtube.com', 'youtu.be']) => 'youtube',
            $host === 'vimeo.com' => 'vimeo',
            $host === 'sketchfab.com' => 'sketchfab',
            $host === 'artstation.com' => 'artstation',
            in_array($host, ['soundcloud.com', 'spotify.com']) => 'audio_provider',
            // Página de Pinterest (pin): usar oEmbed
            in_array($host, ['pinterest.com', 'ar.pinterest.com', 'es.pinterest.com', 'pin.it']) => 'pinterest',
            // Imágenes directas de Pinterest (i.pinimg.com) o Imgur: renderizar como <img>
            in_array($host, ['i.pinimg.com', 'imgur.com', 'i.imgur.com']) => 'image_direct',
            // Otros (DeviantArt, etc.)
            $host === 'deviantart.com' => 'image_provider',
            in_array($host, ['drive.google.com', 'docs.google.com']) => 'google_drive',
            default => 'unknown',
        };
    }

    /**
     * Determina la categoría general (video, 3d, image, etc.)
     */
    protected function determineCategory(string $provider): string
    {
        return match ($provider) {
            'youtube', 'vimeo' => 'video',
            'sketchfab' => '3d',
            'audio_provider' => 'audio',
            'google_drive' => 'document',
            default => 'image',
        };
    }

    /**
     * Determina cómo el frontend debe renderizar esto.
     */
    protected function determineRenderType(string $category): string
    {
        return match ($category) {
            'video', '3d', 'audio', 'document' => 'iframe',
            default => 'image',
        };
    }

    /**
     * Genera el HTML del embed para proveedores que usan iframe.
     */
    protected function generateEmbedHtml(string $provider, string $url): ?string
    {
        // RNF-04: Sandboxing de iFrames
        $sandbox = 'sandbox="allow-scripts allow-same-origin allow-presentation"';

        // YouTube: transformar URL de watch a embed
        if ($provider === 'youtube') {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $videoId = $params['v'] ?? null;

            // Soporte para youtu.be/VIDEO_ID
            if (!$videoId) {
                $path = trim(parse_url($url, PHP_URL_PATH), '/');
                $videoId = $path ?: null;
            }

            if ($videoId) {
                $embedUrl = 'https://www.youtube.com/embed/' . htmlspecialchars($videoId);
                return '<iframe src="' . $embedUrl . '" ' . $sandbox . ' frameborder="0" allowfullscreen></iframe>';
            }
        }

        if (in_array($provider, ['vimeo', 'sketchfab'])) {
            return '<iframe src="' . htmlspecialchars($url) . '" ' . $sandbox . ' frameborder="0" allowfullscreen></iframe>';
        }

        return null;
    }
}

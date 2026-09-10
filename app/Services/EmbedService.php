<?php

namespace App\Services;

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
        'pin.it',
        'deviantart.com',
        'drive.google.com',
        'docs.google.com',
        'imgur.com' // Agregado imgur para tests de imágenes directas comunes
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
        
        // 5. Generar código embed preliminar
        $embedHtml = $this->generateEmbedHtml($provider, $url);

        return [
            'provider'      => $provider,
            'category'      => $category,
            'render_type'   => $renderType,
            'embed_html'    => $embedHtml,
            'thumbnail_url' => null, // Espacio para futura integración de oEmbed
        ];
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
            in_array($host, ['pinterest.com', 'pin.it', 'deviantart.com', 'imgur.com']) => 'image_provider',
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
     * Genera el HTML inicial del embed si aplica.
     */
    protected function generateEmbedHtml(string $provider, string $url): ?string
    {
        // RNF-04: Sandboxing de iFrames
        $sandbox = 'sandbox="allow-scripts allow-same-origin allow-presentation"';

        if (in_array($provider, ['youtube', 'vimeo', 'sketchfab'])) {
            // Nota: Para una app en producción habría que transformar las URLs de YouTube (watch?v=) 
            // al formato embed (embed/), pero almacenaremos la base para que el frontend lo maneje,
            // o generamos un HTML genérico seguro.
            return '<iframe src="' . htmlspecialchars($url) . '" ' . $sandbox . ' frameborder="0" allowfullscreen></iframe>';
        }

        return null;
    }
}

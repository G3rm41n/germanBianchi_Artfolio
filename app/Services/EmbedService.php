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
        'm.youtube.com',
        'vimeo.com',
        'soundcloud.com',
        'spotify.com',
        'open.spotify.com',
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
        $isPinterestHost = $host === 'pin.it' || $host === 'pinterest.com' || str_ends_with($host, '.pinterest.com') || str_ends_with($host, '.pinimg.com');
        $isDeviantArtHost = $host === 'deviantart.com' || str_ends_with($host, '.deviantart.com') || $host === 'sta.sh';
        $isSpotifyHost = $host === 'spotify.com' || str_ends_with($host, '.spotify.com');
        $isYouTubeHost = in_array($host, ['youtube.com', 'youtu.be']) || str_ends_with($host, '.youtube.com');
        $isSketchfabHost = $host === 'sketchfab.com' || str_ends_with($host, '.sketchfab.com');
        if (!in_array($host, $this->allowedDomains) && !$isPinterestHost && !$isDeviantArtHost && !$isSpotifyHost && !$isYouTubeHost && !$isSketchfabHost) {
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
        $title = null;

        if ($provider === 'pinterest') {
            $pinterestData = $this->fetchPinterestData($url);
            $thumbnailUrl = $pinterestData['image_url'] ?? null;
            $title = $pinterestData['title'] ?? null;
            $renderType = 'image'; // Se renderiza como imagen nativa directa, no iframe
            $embedHtml = null;
        } elseif ($provider === 'deviantart') {
            $deviantArtData = $this->fetchDeviantArtData($url);
            $thumbnailUrl = $deviantArtData['image_url'] ?? null;
            $title = $deviantArtData['title'] ?? null;
            $renderType = 'image'; // Se renderiza como imagen nativa directa
            $embedHtml = null;
        } elseif ($provider === 'sketchfab') {
            $sketchfabData = $this->fetchSketchfabData($url);
            $thumbnailUrl = $sketchfabData['image_url'] ?? null;
            $title = $sketchfabData['title'] ?? null;
            $renderType = 'image'; // Se renderiza como vista previa de render 3D
            $embedHtml = null;
        } elseif ($provider === 'youtube') {
            $youtubeData = $this->fetchYouTubeData($url);
            $thumbnailUrl = $youtubeData['image_url'] ?? null;
            $title = $youtubeData['title'] ?? null;
            $embedHtml = $youtubeData['embed_html'] ?? null;
            $renderType = 'iframe';
        } elseif ($provider === 'spotify') {
            $spotifyData = $this->fetchSpotifyData($url);
            $thumbnailUrl = $spotifyData['image_url'] ?? null;
            $title = $spotifyData['title'] ?? null;
            $embedHtml = $spotifyData['embed_html'] ?? null;
            $renderType = 'iframe';
        } elseif (in_array($provider, ['google_docs', 'google_drive'])) {
            $docsData = $this->fetchGoogleDocsData($url);
            $thumbnailUrl = $docsData['image_url'] ?? null;
            $title = $docsData['title'] ?? null;
            $embedHtml = $docsData['embed_html'] ?? null;
            $renderType = 'iframe';
        } elseif ($provider === 'image_direct') {
            // Es un enlace directo a un archivo de imagen (i.pinimg.com, imgur.com, etc.)
            if (str_contains($host, 'pinimg.com')) {
                // Si viene de Pinterest CDN, asegurar resolución originals
                $thumbnailUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $url);
            } else {
                $thumbnailUrl = $url;
            }
            $renderType = 'image';
        } else {
            $embedHtml = $this->generateEmbedHtml($provider, $url);
        }

        return [
            'provider'      => $provider,
            'category'      => $category,
            'render_type'   => $renderType,
            'embed_html'    => $embedHtml,
            'thumbnail_url' => $thumbnailUrl,
            'title'         => $title,
        ];
    }

    /**
     * Extrae los metadatos y la imagen en máxima resolución (originals / 736x) de un Pin de Pinterest.
     * Retorna ['image_url' => string|null, 'title' => string|null]
     */
    protected function fetchPinterestData(string $url): array
    {
        $imageUrl = null;
        $title = null;

        // Si es un enlace acortado pin.it, resolver la redirección
        if (str_contains($url, 'pin.it')) {
            try {
                $response = Http::timeout(4)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])->get($url);
                $effectiveUri = $response->effectiveUri();
                if ($effectiveUri) {
                    $url = (string) $effectiveUri;
                }
            } catch (\Exception $e) {
                // Continuar con la URL original
            }
        }

        // 1. Intento primario: API pública oEmbed de Pinterest (rápido y sin captchas)
        try {
            $oembedEndpoint = 'https://www.pinterest.com/oembed.json';
            $response = Http::timeout(5)->get($oembedEndpoint, ['url' => $url]);

            if ($response->successful()) {
                $data = $response->json();
                $rawThumb = $data['thumbnail_url'] ?? null;
                $titleCandidate = !empty(trim($data['title'] ?? '')) ? trim($data['title']) : null;
                if ($titleCandidate) {
                    $title = $titleCandidate;
                } elseif (!empty($data['author_name'])) {
                    $title = 'Obra de ' . trim($data['author_name']) . ' en Pinterest';
                }

                if ($rawThumb) {
                    // Reemplazar la resolución baja de miniatura (236x) por originals
                    $candidateOriginal = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $rawThumb);
                    $candidate736 = preg_replace('#/(236x|474x|564x)/#', '/736x/', $rawThumb);

                    // Verificación rápida HEAD de originals (timeout 2s)
                    try {
                        $headCheck = Http::timeout(2)->head($candidateOriginal);
                        $imageUrl = $headCheck->successful() ? $candidateOriginal : $candidate736;
                    } catch (\Exception $e) {
                        $imageUrl = $candidateOriginal; // fallback optimista a originals
                    }
                }
            }
        } catch (\Exception $e) {
            // Continuar con fallback HTML si oEmbed falla
        }

        // 2. Fallback secundario: lectura de metadatos HTML (JSON-LD y OpenGraph)
        if (!$imageUrl) {
            try {
                $response = Http::timeout(5)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // Buscar en JSON-LD (donde suele residir la imagen originals)
                    if (preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
                        foreach ($matches[1] as $jsonLd) {
                            $decoded = json_decode($jsonLd, true);
                            if (isset($decoded['image'])) {
                                $imgVal = is_array($decoded['image']) ? ($decoded['image']['contentUrl'] ?? reset($decoded['image'])) : $decoded['image'];
                                if (is_string($imgVal) && filter_var($imgVal, FILTER_VALIDATE_URL)) {
                                    $imageUrl = $imgVal;
                                    break;
                                }
                            }
                            if (!$title && !empty($decoded['headline'])) {
                                $title = $decoded['headline'];
                            }
                        }
                    }

                    // Buscar en og:image si JSON-LD no dio la URL
                    if (!$imageUrl && preg_match('/<meta[^>]+(?:property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']|content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\'])/i', $html, $m)) {
                        $ogImage = !empty($m[1]) ? $m[1] : (!empty($m[2]) ? $m[2] : null);
                        if ($ogImage) {
                            $imageUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $ogImage);
                        }
                    }

                    // Título en OpenGraph
                    if (!$title && preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $title = trim($m[1]);
                    }
                }
            } catch (\Exception $e) {
                // Silencioso
            }
        }

        return [
            'image_url' => $imageUrl,
            'title'     => $title,
        ];
    }

    /**
     * Extrae los metadatos y la imagen en alta resolución de una obra de DeviantArt.
     * Retorna ['image_url' => string|null, 'title' => string|null]
     */
    protected function fetchDeviantArtData(string $url): array
    {
        $imageUrl = null;
        $title = null;

        // 1. Intento primario: API pública oEmbed de DeviantArt
        try {
            $oembedEndpoint = 'https://backend.deviantart.com/oembed';
            $response = Http::timeout(5)->get($oembedEndpoint, ['url' => $url]);

            if ($response->successful()) {
                $data = $response->json();
                // DeviantArt devuelve la imagen en alta resolución en 'url' o en 'thumbnail_url'
                $imageUrl = $data['url'] ?? ($data['thumbnail_url'] ?? null);
                $titleCandidate = !empty(trim($data['title'] ?? '')) ? trim($data['title']) : null;
                $author = !empty($data['author_name']) ? trim($data['author_name']) : null;

                if ($titleCandidate && $author) {
                    $title = $titleCandidate . ' - por ' . $author;
                } elseif ($titleCandidate) {
                    $title = $titleCandidate;
                } elseif ($author) {
                    $title = 'Obra de ' . $author . ' en DeviantArt';
                }
            }
        } catch (\Exception $e) {
            // Continuar con fallback HTML si oEmbed falla
        }

        // 2. Fallback secundario: lectura de metadatos HTML (OpenGraph)
        if (!$imageUrl) {
            try {
                $response = Http::timeout(5)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // og:image
                    if (preg_match('/<meta[^>]+(?:property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']|content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\'])/i', $html, $m)) {
                        $imageUrl = !empty($m[1]) ? $m[1] : (!empty($m[2]) ? $m[2] : null);
                    }

                    // og:title
                    if (!$title && preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $title = trim($m[1]);
                    }
                }
            } catch (\Exception $e) {
                // Silencioso
            }
        }

        return [
            'image_url' => $imageUrl,
            'title'     => $title,
        ];
    }

    /**
     * Extrae los metadatos y la imagen de render 3D en alta resolución de Sketchfab.
     * Retorna ['image_url' => string|null, 'title' => string|null]
     */
    protected function fetchSketchfabData(string $url): array
    {
        $imageUrl = null;
        $title = null;

        // 1. Intento primario: API pública oEmbed de Sketchfab
        try {
            $oembedEndpoint = 'https://sketchfab.com/oembed';
            $response = Http::timeout(5)->get($oembedEndpoint, ['url' => $url]);

            if ($response->successful()) {
                $data = $response->json();
                $imageUrl = $data['thumbnail_url'] ?? null;
                $titleCandidate = !empty(trim($data['title'] ?? '')) ? trim($data['title']) : null;
                $author = !empty($data['author_name']) ? trim($data['author_name']) : null;

                if ($titleCandidate && $author) {
                    $title = $titleCandidate . ' - por ' . $author;
                } elseif ($titleCandidate) {
                    $title = $titleCandidate;
                } elseif ($author) {
                    $title = 'Modelo 3D de ' . $author . ' en Sketchfab';
                }
            }
        } catch (\Exception $e) {
            // Continuar con fallback HTML si oEmbed falla
        }

        // 2. Fallback secundario: lectura de metadatos HTML (OpenGraph og:image a 1024x768)
        if (!$imageUrl) {
            try {
                $response = Http::timeout(5)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // og:image
                    if (preg_match('/<meta[^>]+(?:property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']|content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\'])/i', $html, $m)) {
                        $imageUrl = !empty($m[1]) ? $m[1] : (!empty($m[2]) ? $m[2] : null);
                    }

                    // og:title
                    if (!$title && preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $title = trim($m[1]);
                    }
                }
            } catch (\Exception $e) {
                // Silencioso
            }
        }

        return [
            'image_url' => $imageUrl,
            'title'     => $title,
        ];
    }

    /**
     * Extrae los metadatos, miniatura HD y embed HTML de un video de YouTube.
     * Retorna ['image_url' => string|null, 'title' => string|null, 'embed_html' => string|null]
     */
    protected function fetchYouTubeData(string $url): array
    {
        $videoId = null;
        if (preg_match('#(?:youtube\.com/(?:watch\?.*v=|shorts/|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            $videoId = $m[1];
        }

        if (!$videoId) {
            return [
                'image_url'  => null,
                'title'      => null,
                'embed_html' => null,
            ];
        }

        $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        $title = null;

        // 1. oEmbed para obtener título y autor
        try {
            $response = Http::timeout(4)->get('https://www.youtube.com/oembed', [
                'url' => "https://www.youtube.com/watch?v={$videoId}",
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $titleCandidate = !empty(trim($data['title'] ?? '')) ? trim($data['title']) : null;
                $author = !empty(trim($data['author_name'] ?? '')) ? trim($data['author_name']) : null;

                if ($titleCandidate && $author) {
                    $title = $titleCandidate . ' - por ' . $author;
                } else {
                    $title = $titleCandidate;
                }
            }
        } catch (\Exception $e) {
            // Silencioso
        }

        // 2. Comprobar disponibilidad de maxresdefault (fallback a hqdefault si maxres devuelve 404)
        try {
            $thumbCheck = Http::timeout(3)->head($thumbnailUrl);
            if ($thumbCheck->status() === 404) {
                $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }
        } catch (\Exception $e) {
            $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        }

        // 3. Iframe embed responsivo 16:9 con sandboxing RNF-04
        $sandbox = 'sandbox="allow-scripts allow-same-origin allow-presentation"';
        $embedHtml = '<iframe src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" ' . $sandbox . ' frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>';

        return [
            'image_url'  => $thumbnailUrl,
            'title'      => $title,
            'embed_html' => $embedHtml,
        ];
    }

    /**
     * Extrae los metadatos, portada oficial y widget interactivo de Spotify.
     * Retorna ['image_url' => string|null, 'title' => string|null, 'embed_html' => string|null]
     */
    protected function fetchSpotifyData(string $url): array
    {
        $thumbnailUrl = null;
        $title = null;
        $embedHtml = null;

        try {
            $response = Http::timeout(5)->get('https://open.spotify.com/oembed', ['url' => $url]);

            if ($response->successful()) {
                $data = $response->json();
                $thumbnailUrl = $data['thumbnail_url'] ?? null;
                $titleCandidate = !empty(trim($data['title'] ?? '')) ? trim($data['title']) : null;
                if ($titleCandidate) {
                    $title = $titleCandidate;
                }
                $embedHtml = $data['html'] ?? null;
            }
        } catch (\Exception $e) {
            // Continuar con fallback manual
        }

        // Si oEmbed no retornó embedHtml, construir el iframe oficial a partir del ID y tipo
        if (!$embedHtml && preg_match('#open\.spotify\.com/(track|album|playlist|episode|show)/([a-zA-Z0-9]+)#i', $url, $m)) {
            $type = $m[1];
            $id = $m[2];
            $embedHtml = '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/' . htmlspecialchars($type) . '/' . htmlspecialchars($id) . '" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-presentation allow-popups" loading="lazy"></iframe>';
        }

        return [
            'image_url'  => $thumbnailUrl,
            'title'      => $title,
            'embed_html' => $embedHtml,
        ];
    }

    /**
     * Extrae los metadatos, miniatura de Drive y visor /preview de Google Docs / Google Drive.
     * Retorna ['image_url' => string|null, 'title' => string|null, 'embed_html' => string|null]
     */
    protected function fetchGoogleDocsData(string $url): array
    {
        $docId = null;
        $docType = 'document';

        if (preg_match('#docs\.google\.com/([a-zA-Z0-9_-]+)/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $docType = strtolower($m[1]); // document, presentation, spreadsheets
            $docId = $m[2];
        } elseif (preg_match('#drive\.google\.com/file/d/([a-zA-Z0-9_-]+)#i', $url, $m)) {
            $docType = 'file';
            $docId = $m[1];
        }

        if (!$docId) {
            return [
                'image_url'  => null,
                'title'      => null,
                'embed_html' => null,
            ];
        }

        $previewUrl = ($docType === 'file')
            ? "https://drive.google.com/file/d/{$docId}/preview"
            : "https://docs.google.com/{$docType}/d/{$docId}/preview";

        $thumbnailUrl = "https://drive.google.com/thumbnail?id={$docId}&sz=w800";
        $title = null;

        // Intentar leer og:title o <title> desde la URL
        try {
            $response = Http::timeout(4)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->get($previewUrl);

            if ($response->successful()) {
                $html = $response->body();
                if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                    $titleCandidate = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    if (!empty($titleCandidate)) {
                        $title = $titleCandidate;
                    }
                } elseif (preg_match('/<title>([^<]+)<\/title>/i', $html, $m)) {
                    $titleCandidate = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $titleCandidate = preg_replace('/\s*-\s*Google (Docs|Drive|Sheets|Slides)\s*$/i', '', $titleCandidate);
                    if (!empty($titleCandidate) && !str_contains($titleCandidate, 'Google Drive') && !str_contains($titleCandidate, 'Google Docs')) {
                        $title = $titleCandidate;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silencioso
        }

        if (!$title) {
            $title = match ($docType) {
                'presentation' => 'Presentación de Google Slides',
                'spreadsheets' => 'Hoja de Cálculo de Google Sheets',
                'file' => 'Archivo / PDF en Google Drive',
                default => 'Documento de Google Docs',
            };
        }

        $sandbox = 'sandbox="allow-scripts allow-same-origin allow-presentation"';
        $embedHtml = '<iframe src="' . htmlspecialchars($previewUrl) . '" ' . $sandbox . ' frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>';

        return [
            'image_url'  => $thumbnailUrl,
            'title'      => $title,
            'embed_html' => $embedHtml,
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
            $host === 'sketchfab.com' || str_ends_with($host, '.sketchfab.com') => 'sketchfab',
            $host === 'artstation.com' => 'artstation',
            $host === 'spotify.com' || str_ends_with($host, '.spotify.com') => 'spotify',
            $host === 'soundcloud.com' || str_ends_with($host, '.soundcloud.com') => 'soundcloud',
            // Página de Pinterest (pin)
            $host === 'pin.it' || $host === 'pinterest.com' || str_ends_with($host, '.pinterest.com') => 'pinterest',
            // Página de DeviantArt
            $host === 'deviantart.com' || str_ends_with($host, '.deviantart.com') || $host === 'sta.sh' => 'deviantart',
            // Imágenes directas de Pinterest (i.pinimg.com) o Imgur: renderizar como <img>
            str_ends_with($host, 'pinimg.com') || in_array($host, ['imgur.com', 'i.imgur.com']) => 'image_direct',
            in_array($host, ['drive.google.com', 'docs.google.com']) => 'google_docs',
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
            'spotify', 'soundcloud', 'audio_provider' => 'audio',
            'google_docs', 'google_drive' => 'doc',
            default => 'image',
        };
    }

    /**
     * Determina cómo el frontend debe renderizar esto.
     */
    protected function determineRenderType(string $category): string
    {
        return match ($category) {
            'video', '3d', 'audio', 'doc' => 'iframe',
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

        if ($provider === 'youtube') {
            $yt = $this->fetchYouTubeData($url);
            return $yt['embed_html'] ?? null;
        }

        if ($provider === 'spotify') {
            $sp = $this->fetchSpotifyData($url);
            return $sp['embed_html'] ?? null;
        }

        if (in_array($provider, ['google_docs', 'google_drive'])) {
            $doc = $this->fetchGoogleDocsData($url);
            return $doc['embed_html'] ?? null;
        }

        if (in_array($provider, ['vimeo', 'sketchfab'])) {
            return '<iframe src="' . htmlspecialchars($url) . '" ' . $sandbox . ' frameborder="0" allowfullscreen></iframe>';
        }

        return null;
    }
}

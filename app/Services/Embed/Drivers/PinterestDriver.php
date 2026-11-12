<?php

namespace App\Services\Embed\Drivers;

class PinterestDriver extends AbstractEmbedDriver
{
    public function getProviderName(): string
    {
        return 'pinterest';
    }

    public function matches(string $host, string $url): bool
    {
        return $host === 'pin.it' 
            || $host === 'pinterest.com' 
            || str_ends_with($host, '.pinterest.com')
            || str_ends_with($host, '.pinimg.com');
    }

    public function process(string $url, string $host): ?array
    {
        // Enlaces directos a archivos CDN de imagen (ej. i.pinimg.com/...)
        if (str_ends_with($host, 'pinimg.com')) {
            $thumbnailUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $url);
            return [
                'provider'      => 'image_direct',
                'category'      => 'image',
                'render_type'   => 'image',
                'embed_html'    => null,
                'thumbnail_url' => $thumbnailUrl,
                'title'         => 'Pinterest Image',
            ];
        }

        $realUrl = $url;
        // 1. Si es pin.it (shortlink), resolver la redirección final
        if ($host === 'pin.it') {
            try {
                $headRes = \Illuminate\Support\Facades\Http::timeout(4)->withoutRedirecting()->head($url);
                $location = $headRes->header('Location');
                if ($location) {
                    $realUrl = $location;
                }
            } catch (\Throwable) {
                // Si falla el HEAD, continuamos con la URL original
            }
        }

        // 2. Extraer pinId si está presente en la ruta /pin/{id}/
        $pinId = null;
        if (preg_match('#/pin/(\d+)#', $realUrl, $m)) {
            $pinId = $m[1];
        }

        $imageUrl = null;
        $title = null;

        // 3. Probar oEmbed oficial de Pinterest (rápido y confiable)
        $oembedEndpoint = 'https://www.pinterest.com/oembed.json?url=' . urlencode($realUrl);
        $oembedRes = $this->safeHttpGet($oembedEndpoint);
        if ($oembedRes && $oembedRes->json()) {
            $oembedData = $oembedRes->json();
            $title = $oembedData['title'] ?? null;
            if (!empty($oembedData['thumbnail_url'])) {
                // Transformar thumbnail a máxima resolución originals / 736x
                $imageUrl = preg_replace('#/(150x150|236x|474x|564x)/#', '/originals/', $oembedData['thumbnail_url']);
            }
        }

        // 4. Si no obtuvimos imagen completa vía oEmbed, hacer fetch con User-Agent
        if (!$imageUrl) {
            $pageRes = $this->safeHttpGet($realUrl);
            if ($pageRes) {
                $html = $pageRes->body();

                // Intentar extraer el título de <meta property="og:title">
                if (!$title) {
                    $title = $this->extractMetaTag($html, 'og:title');
                }

                // Intentar extraer og:image
                $ogImage = $this->extractMetaTag($html, 'og:image');
                if ($ogImage) {
                    // Forzar reemplazo a originals para tener máxima calidad
                    $imageUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $ogImage);
                }

                // Fallback en JSON-LD embebido en la página
                if (!$imageUrl && preg_match('#"image":\s*"([^"]+)"#i', $html, $jsonMatch)) {
                    $imageUrl = stripslashes($jsonMatch[1]);
                    $imageUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $imageUrl);
                }

                // Fallback robusto en HTML react/SSR de Pinterest: buscar cualquier imagen de i.pinimg.com en originals o 736x
                if (!$imageUrl && preg_match('#https://i\.pinimg\.com/(?:originals|[a-zA-Z0-9]+)/[a-zA-Z0-9/_.-]+\.(?:jpg|jpeg|png|webp)#i', $html, $pinMatch)) {
                    $imageUrl = preg_replace('#/(236x|474x|564x|736x)/#', '/originals/', $pinMatch[0]);
                }
            }
        }

        // Limpiar sufijo "| Pinterest" del título
        $title = $this->cleanTitle($title, ['| Pinterest', '- Pinterest']);

        return [
            'provider'      => $this->getProviderName(),
            'category'      => 'image',
            'render_type'   => 'image',
            'embed_html'    => null,
            'thumbnail_url' => $imageUrl,
            'title'         => $title,
        ];
    }
}


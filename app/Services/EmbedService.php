<?php

namespace App\Services;

use App\Services\Embed\Contracts\EmbedDriverInterface;
use App\Services\Embed\Drivers\ArtStationDriver;
use App\Services\Embed\Drivers\BandcampDriver;
use App\Services\Embed\Drivers\CodePenDriver;
use App\Services\Embed\Drivers\DeviantArtDriver;
use App\Services\Embed\Drivers\DirectImageDriver;
use App\Services\Embed\Drivers\GoogleDocsDriver;
use App\Services\Embed\Drivers\PinterestDriver;
use App\Services\Embed\Drivers\SketchfabDriver;
use App\Services\Embed\Drivers\SoundCloudDriver;
use App\Services\Embed\Drivers\SpotifyDriver;
use App\Services\Embed\Drivers\VimeoDriver;
use App\Services\Embed\Drivers\YouTubeDriver;

class EmbedService
{
    /**
     * @var EmbedDriverInterface[]
     */
    protected array $drivers = [];

    public function __construct()
    {
        $this->registerDefaultDrivers();
    }

    /**
     * Registra los drivers predeterminados del sistema.
     */
    protected function registerDefaultDrivers(): void
    {
        $this->drivers = [
            new YouTubeDriver(),
            new PinterestDriver(),
            new DeviantArtDriver(),
            new SketchfabDriver(),
            new SpotifyDriver(),
            new GoogleDocsDriver(),
            new VimeoDriver(),
            new SoundCloudDriver(),
            new CodePenDriver(),
            new BandcampDriver(),
            new ArtStationDriver(),
            new DirectImageDriver(),
        ];
    }

    /**
     * Permite registrar o extender drivers dinámicamente en tiempo de ejecución.
     */
    public function registerDriver(EmbedDriverInterface $driver, bool $prepend = false): void
    {
        if ($prepend) {
            array_unshift($this->drivers, $driver);
        } else {
            $this->drivers[] = $driver;
        }
    }

    /**
     * Valida y procesa una URL devolviendo sus metadatos y código embed.
     * Retorna null si el dominio no está soportado o no es seguro.
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

        // Seguridad: Prevención SSRF básica (descartar IPs y localhost)
        if (filter_var($host, FILTER_VALIDATE_IP) !== false || $host === 'localhost') {
            return null;
        }

        // Buscar el driver responsable para este host/URL
        foreach ($this->drivers as $driver) {
            if ($driver->matches($host, $url)) {
                $result = $driver->process($url, $host);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }
}

<?php

namespace App\Services\Embed\Contracts;

interface EmbedDriverInterface
{
    /**
     * Determina si este driver es capaz de procesar el host/URL especificado.
     */
    public function matches(string $host, string $url): bool;

    /**
     * Procesa la URL y retorna un array con metadatos estructurados:
     * [
     *   'provider'      => string,
     *   'category'      => string ('image'|'video'|'3d'|'audio'|'doc'|'code'),
     *   'render_type'   => string ('image'|'iframe'),
     *   'embed_html'    => string|null,
     *   'thumbnail_url' => string|null,
     *   'title'         => string|null,
     * ]
     */
    public function process(string $url, string $host): ?array;

    /**
     * Identificador único de este proveedor (youtube, pinterest, sketchfab, etc.)
     */
    public function getProviderName(): string;
}


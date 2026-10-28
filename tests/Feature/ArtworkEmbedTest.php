<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use App\Services\EmbedService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArtworkEmbedTest extends TestCase
{
    use RefreshDatabase;

    protected EmbedService $embedService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->embedService = new EmbedService();
    }

    /**
     * Whitelisted domains are accepted and categorized by provider.
     */
    public function test_whitelisted_providers_are_accepted(): void
    {
        Http::fake([
            'www.youtube.com/oembed*' => Http::response(['title' => 'Video Test', 'thumbnail_url' => 'https://img.youtube.com/thumb.jpg', 'html' => '<iframe></iframe>'], 200),
            'open.spotify.com/oembed*' => Http::response(['title' => 'Track Test', 'thumbnail_url' => 'https://spotify.com/thumb.jpg', 'html' => '<iframe></iframe>'], 200),
            'sketchfab.com/oembed*' => Http::response(['title' => '3D Model', 'thumbnail_url' => 'https://sketchfab.com/thumb.jpg'], 200),
            'backend.deviantart.com/oembed*' => Http::response(['title' => 'Art Test', 'thumbnail_url' => 'https://images.deviantart.com/thumb.jpg'], 200),
            'www.pinterest.com/oembed*' => Http::response(['title' => 'Pin Test', 'thumbnail_url' => 'https://i.pinimg.com/thumb.jpg'], 200),
        ]);

        $testCases = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ' => ['provider' => 'youtube', 'category' => 'video'],
            'https://youtu.be/dQw4w9WgXcQ' => ['provider' => 'youtube', 'category' => 'video'],
            'https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT' => ['provider' => 'spotify', 'category' => 'audio'],
            'https://sketchfab.com/3d-models/steampunk-gun-12345' => ['provider' => 'sketchfab', 'category' => '3d'],
            'https://www.deviantart.com/artist/art/Artwork-12345' => ['provider' => 'deviantart', 'category' => 'image'],
            'https://www.pinterest.com/pin/123456789/' => ['provider' => 'pinterest', 'category' => 'image'],
            'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit' => ['provider' => 'google_docs', 'category' => 'doc'],
        ];

        foreach ($testCases as $url => $expected) {
            $result = $this->embedService->processUrl($url);
            $this->assertNotNull($result, "La URL {$url} debería ser aceptada por la whitelist.");
            $this->assertEquals($expected['provider'], $result['provider'], "Proveedor incorrecto para {$url}");
            $this->assertEquals($expected['category'], $result['category'], "Categoría incorrecta para {$url}");
        }
    }

    /**
     * Domains outside the whitelist are rejected.
     */
    public function test_unauthorized_external_domains_are_rejected(): void
    {
        $disallowedUrls = [
            'https://malicious-site.com/art.jpg',
            'https://phishing-portal.net/login',
            'https://random-unverified-blog.org/post/1',
            'https://not-youtube.com/watch?v=123',
            'https://youtube.com.attacker.com/watch?v=123',
        ];

        foreach ($disallowedUrls as $url) {
            $result = $this->embedService->processUrl($url);
            $this->assertNull($result, "La URL no autorizada {$url} debe ser rechazada.");
        }
    }

    /**
     * SSRF attack mitigation (internal IPs, localhost, metadata endpoints).
     */
    public function test_ssrf_attacks_and_internal_ips_are_strictly_rejected(): void
    {
        $ssrfUrls = [
            'http://localhost/admin',
            'http://127.0.0.1:8000/internal',
            'http://169.254.169.254/latest/meta-data/',
            'http://192.168.1.1/router-settings',
            'http://10.0.0.1/secrets',
            'http://0.0.0.0:3306',
        ];

        foreach ($ssrfUrls as $url) {
            $result = $this->embedService->processUrl($url);
            $this->assertNull($result, "La URL potencial de SSRF {$url} debe retornar null.");
        }
    }

    /**
     * Artwork store endpoint validates external URLs.
     */
    public function test_artwork_store_endpoint_enforces_whitelist(): void
    {
        $artist = User::factory()->create();

        // 1. Intento con URL no permitida
        $responseForbidden = $this->actingAs($artist)->post('/artworks', [
            'title' => 'Obra No Permitida',
            'external_url' => 'https://hacker-cdn.ru/image.png',
            'category' => 'image',
        ]);

        $responseForbidden->assertSessionHasErrors(['external_url']);
        $this->assertDatabaseMissing('artworks', ['title' => 'Obra No Permitida']);

        // 2. Intento con URL válida de YouTube
        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Animación 3D en YouTube',
                'thumbnail_url' => 'https://i.ytimg.com/vi/test/hqdefault.jpg',
                'html' => '<iframe src="https://www.youtube.com/embed/test"></iframe>',
            ], 200),
        ]);

        $responseSuccess = $this->actingAs($artist)->post('/artworks', [
            'title' => 'Animación 3D en YouTube',
            'description' => 'Demo reel publicado en YouTube.',
            'external_url' => 'https://www.youtube.com/watch?v=validId123',
            'category' => 'video',
            'status' => 'published',
        ]);

        $responseSuccess->assertRedirect('/artworks');
        $this->assertDatabaseHas('artworks', [
            'title' => 'Animación 3D en YouTube',
            'provider' => 'youtube',
            'category' => 'video',
            'status' => 'published',
        ]);
    }
}

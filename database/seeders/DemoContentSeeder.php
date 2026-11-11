<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artwork;
use App\Models\Collection;
use App\Models\Bookmark;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    /**
     * Semilla con usuarios artistas de prueba, obras multimedia de dominio público,
     * colecciones organizadas, favoritos y reportes de muestra para testing completo.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ─────────────────────────────────────────────────────────────────────
        // 1. ARTISTAS DE DEMOSTRACIÓN (8 CREADORES MULTIDISCIPLINARIOS)
        // ─────────────────────────────────────────────────────────────────────

        $artistsData = [
            // ── 1. Elena Rostova (3D & Escultura Digital) ──────────────────
            [
                'name'              => 'Elena Rostova',
                'email'             => 'elena@artfolio.test',
                'slug'              => 'elena_rostova',
                'bio'               => "Digital sculptor and 3D artist specializing in anatomical reinterpretaciones, classical Hellenic statuary, and heroic fantasy props for video games.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
                'crop_x'            => 0,
                'crop_y'            => -5,
                'crop_zoom'         => 1.2,
                'collections'       => [
                    [
                        'name'        => 'Classical Sculptures & Archaeology',
                        'description' => 'Real-time 3D models inspired by Roman antiquity, mythology, and museum relics.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Hellenic Marble Bust Study',
                        'description'   => 'Cloth draping and facial anatomy exploration. High-poly sculpt baked onto low-poly mesh for real-time engines.',
                        'price_hint'    => 'Ref: $120 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/helenic-sculpture-asset',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119',
                        'embed_html'    => '<iframe title="Hellenic Bust" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Classical Bronze Relief Panel',
                        'description'   => 'Three-dimensional frieze study with oxidized verdigris patina and ornamental foliage.',
                        'price_hint'    => 'Ref: $95 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/bronze-relief-classical',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1563089145-599997674d42',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Valkyrie Ceremonial Helmet',
                        'description'   => 'Hard-surface game asset with embossed Nordic knotwork and weathered silver plating.',
                        'price_hint'    => 'Ref: $160 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/valkyrie-helmet-prop',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 2. Marcus Vance (Concept Art & Matte Painting) ────────────
            [
                'name'              => 'Marcus Vance',
                'email'             => 'marcus@artfolio.test',
                'slug'              => 'marcus_vance',
                'bio'               => "Senior concept artist and matte painter. Exploring atmospheric fantasy realms, monumental architecture, and evocative sci-fi environments.",
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Epic Vistas & Worldbuilding',
                        'description' => 'Panoramic landscapes and keyframe concept paintings for cinematic productions.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Temple of Celestial Nebulas',
                        'description'   => 'Digital painting of an ancestral shrine perched among high mountain ridges during a nebula storm.',
                        'price_hint'    => 'Ref: $150 USD',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Temple-of-Nebulas-982101',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Knight of the Setting Sun',
                        'description'   => 'Character design showcasing ceremonial gilded plate armor inspired by Renaissance heraldry.',
                        'price_hint'    => 'Ref: $110 USD',
                        'external_url'  => 'https://www.pinterest.com/pin/109212345678912345/',
                        'provider'      => 'pinterest',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'The Outpost at Obsidian Ridge',
                        'description'   => 'Sci-fi border outpost under torrential alien precipitation. Color key research for lighting mood.',
                        'price_hint'    => 'Ref: $135 USD',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Obsidian-Outpost-982102',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 3. Sofía Alarcón (Animación 2D, 3D & Video) ───────────────
            [
                'name'              => 'Sofía Alarcón',
                'email'             => 'sofia@artfolio.test',
                'slug'              => 'sofia_alarcon',
                'bio'               => "Independent animation director and motion designer. Passionate about open-source production pipelines, stylized character rigs, and emotional shorts.",
                'commission_status' => 'closed',
                'avatar_url'        => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330',
                'crop_x'            => -5,
                'crop_y'            => -8,
                'crop_zoom'         => 1.3,
                'collections'       => [
                    [
                        'name'        => 'Cinematic Shorts & Open Films',
                        'description' => 'Animation projects produced under open and creative commons licensing.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Sintel - The Open Animation Movie',
                        'description'   => 'Critically acclaimed 3D fantasy short film produced by the Blender Animation Studio. Original score and orchestral textures.',
                        'price_hint'    => 'Creative Commons CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=eRsGyueVLvQ',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/eRsGyueVLvQ" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Big Buck Bunny - Animated Classic',
                        'description'   => 'Landmark animated comedy highlighting fur rendering, fluid physics, and comic storytelling in open 3D tooling.',
                        'price_hint'    => 'Public Domain / CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/YE7VzlLtp-4" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Tears of Steel - Sci-Fi VFX Showcase',
                        'description'   => 'Open visual effects short blending live-action footage with photorealistic robotics and tracking in futuristic Amsterdam.',
                        'price_hint'    => 'Creative Commons CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=R6MlUcmOul8',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/R6MlUcmOul8" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 4. Lucas Morel (Audio & Diseño Sonoro) ────────────────────
            [
                'name'              => 'Lucas Morel',
                'email'             => 'lucas@artfolio.test',
                'slug'              => 'lucas_morel',
                'bio'               => "Sound designer, audio engineer, and composer. Crafting immersive sonic landscapes, orchestral textures, and modular synthesizers for interactive media.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Acoustic Soundscapes & OST',
                        'description' => 'Original recordings, orchestral experiments, and modular synth sketches.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Symphony No. 5 in C Minor - Masterpiece',
                        'description'   => 'Dynamic orchestral recording of an immortal symphonic work. High-energy motifs for epic background ambiance.',
                        'price_hint'    => 'Public Domain Performance',
                        'external_url'  => 'https://open.spotify.com/track/1Pt5p3vLzJc5x9P3a4L6',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/1Pt5p3vLzJc5x9P3a4L6" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-presentation allow-popups" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Clair de Lune - Nocturnal Impression',
                        'description'   => 'Delicate acoustic piano performance exploring impressionistic harmony, dynamic touch, and atmospheric reverb.',
                        'price_hint'    => 'Open Recording',
                        'external_url'  => 'https://open.spotify.com/track/6N1kxeJE299zYJ5Pj8i9',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1520523839898-50712743e9a7',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/6N1kxeJE299zYJ5Pj8i9" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-presentation allow-popups" loading="lazy"></iframe>',
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 5. Maya Chen (Escritura, Guiones & Documentos de Diseño) ──
            [
                'name'              => 'Maya Chen',
                'email'             => 'maya@artfolio.test',
                'slug'              => 'maya_chen',
                'bio'               => "Narrative designer, game writer, and worldbuilder. Crafting comprehensive game design documents (GDD), branching dialogues, and lore compendiums.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'crop_x'            => 0,
                'crop_y'            => -3,
                'crop_zoom'         => 1.15,
                'collections'       => [
                    [
                        'name'        => 'Game Design & World Lore Bibles',
                        'description' => 'Complete narrative bibles, mechanics breakdown, and interactive dialogue trees.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Aetheria: Comprehensive Game Design Document',
                        'description'   => 'Full design bible covering core loop, combat mechanics, progression systems, and economic balance for an indie RPG.',
                        'price_hint'    => 'Open Industry Template',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Interactive Narrative & Branching Dialogue Guide',
                        'description'   => 'Methodology notes on state tracking, player agency, and non-linear script writing for modern narrative games.',
                        'price_hint'    => 'Free Reference Paper',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 6. Daniel Kim (Pixel Art & Animación Retro) ───────────────
            [
                'name'              => 'Daniel Kim',
                'email'             => 'daniel@artfolio.test',
                'slug'              => 'daniel_kim',
                'bio'               => "Pixel artist and sprite animator. Creating 16-bit cybernetic cityscapes, hand-crafted sprite sheets, and retro indie game aesthetics.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.05,
                'collections'       => [
                    [
                        'name'        => 'Neo-Seoul: 16-Bit Cyberpunk',
                        'description' => 'Animated pixel environments, character sprites, and isometric dioramas.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Rainy Alleyway - Neo-Seoul 2099',
                        'description'   => 'Nostalgic pixel illustration featuring animated neon sign reflections, rain ripples, and steam vents. 320x180 resolution canvas.',
                        'price_hint'    => 'Ref: $85 USD',
                        'external_url'  => 'https://www.pinterest.com/pin/209212345678912346/',
                        'provider'      => 'pinterest',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Mecha Pilot Combat Sprite Sheet',
                        'description'   => 'Full 8-directional running, jumping, and melee attack cycle sprite frames designed for high-frame-rate action platformers.',
                        'price_hint'    => 'Ref: $140 USD',
                        'external_url'  => 'https://www.deviantart.com/danielkim/art/Mecha-Pilot-Sprites-982103',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 7. Chloe Dubois (Diseño 3D & Entornos Stylized) ───────────
            [
                'name'              => 'Chloe Dubois',
                'email'             => 'chloe@artfolio.test',
                'slug'              => 'chloe_dubois',
                'bio'               => "Stylized 3D environment artist. Hand-painted textures, cozy fantasy dioramas, and modular vegetation kits for game engines.",
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1',
                'crop_x'            => 0,
                'crop_y'            => -4,
                'crop_zoom'         => 1.2,
                'collections'       => [
                    [
                        'name'        => 'Cozy Forest Dioramas',
                        'description' => 'Stylized, low-poly hand-textured scenes inspired by French pastoral tales.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Herbalist Cozy Cottage Diorama',
                        'description'   => 'Hand-painted low-poly fantasy cottage featuring tiled roof, smoking chimney, and glowing lantern lighting in real-time.',
                        'price_hint'    => 'Ref: $175 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/herbalist-cottage-stylized',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Enchanted Autumn Windmill',
                        'description'   => 'Modular wooden windmill with rotating sails and golden leaf particle emitters. Optimized geometry for mobile platforms.',
                        'price_hint'    => 'Ref: $130 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/autumn-windmill-lowpoly',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],

            // ── 8. Gabriel Santana (Motion Graphics & Video Experimental) ─
            [
                'name'              => 'Gabriel Santana',
                'email'             => 'gabriel@artfolio.test',
                'slug'              => 'gabriel_santana',
                'bio'               => "Visual artist, motion graphics animator, and generative video creator. Exploring abstract geometries, rhythm synchronization, and kinetic typography.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.1,
                'collections'       => [
                    [
                        'name'        => 'Kinetic Motion & Visual Experiments',
                        'description' => 'Abstract 3D motion design loops and procedural visual rhythm studies.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Spring - Blender Open Movie Project',
                        'description'   => 'Poetic fantasy short showcasing stylized character rendering, fur simulation, and breathtaking alpine nature art.',
                        'price_hint'    => 'Creative Commons CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=WhWc3b3KhnY',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/WhWc3b3KhnY" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Cosmos Laundromat - Procedural Fur & Fabric',
                        'description'   => 'Surrealistic animation short exploring dynamic hair rendering, absurd comedy, and stylized character shading.',
                        'price_hint'    => 'Open Animation Archive',
                        'external_url'  => 'https://www.youtube.com/watch?v=Y-rmzh0PI3c',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/Y-rmzh0PI3c" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                ],
            ],
        ];

        $createdUsers = [];
        $createdArtworks = [];

        foreach ($artistsData as $artistInfo) {
            $user = User::updateOrCreate(
                ['email' => $artistInfo['email']],
                [
                    'name'              => $artistInfo['name'],
                    'slug'              => $artistInfo['slug'],
                    'password'          => $password,
                    'bio'               => $artistInfo['bio'],
                    'commission_status' => $artistInfo['commission_status'],
                    'avatar_url'        => $artistInfo['avatar_url'],
                    'crop_x'            => $artistInfo['crop_x'],
                    'crop_y'            => $artistInfo['crop_y'],
                    'crop_zoom'         => $artistInfo['crop_zoom'],
                    'is_admin'          => false,
                    'status'            => 'active',
                    'email_verified_at' => now(), // Asegurar verificación con MustVerifyEmail
                ]
            );

            $createdUsers[] = $user;

            // Crear colecciones
            $userCollections = [];
            foreach ($artistInfo['collections'] as $colData) {
                $col = Collection::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'name'    => $colData['name'],
                    ],
                    [
                        'description' => $colData['description'],
                    ]
                );
                $userCollections[] = $col;
            }

            // Crear obras
            foreach ($artistInfo['artworks'] as $artData) {
                $artwork = Artwork::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'title'   => $artData['title'],
                    ],
                    [
                        'description'   => $artData['description'],
                        'price_hint'    => $artData['price_hint'],
                        'external_url'  => $artData['external_url'],
                        'provider'      => $artData['provider'],
                        'category'      => $artData['category'],
                        'render_type'   => $artData['render_type'],
                        'thumbnail_url' => $artData['thumbnail_url'],
                        'embed_html'    => $artData['embed_html'],
                        'status'        => 'published',
                        'is_featured'   => $artData['is_featured'],
                    ]
                );

                // Asignar a la colección del artista si existe
                if (!empty($userCollections)) {
                    $artwork->collections()->syncWithoutDetaching([$userCollections[0]->id]);
                }

                $createdArtworks[] = $artwork;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. FAVORITOS DE MUESTRA (Bookmarks cruzados entre artistas)
        // ─────────────────────────────────────────────────────────────────────
        if (count($createdUsers) >= 4 && count($createdArtworks) >= 8) {
            // Elena guarda la obra de Marcus y el video de Sofía
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[3]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[6]->id]
            );

            // Marcus guarda la escultura de Elena y el pixel art de Daniel
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[0]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[13]->id]
            );

            // Daniel guarda el GDD de Maya y el video de Gabriel
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[11]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[17]->id]
            );

            // Chloe guarda la escultura de Elena y el paisaje de Marcus
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[0]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[4]->id]
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. REPORTE DE MUESTRA PARA EL PANEL DE ADMINISTRACIÓN (RF-09)
        // ─────────────────────────────────────────────────────────────────────
        if (count($createdUsers) >= 3 && count($createdArtworks) >= 2) {
            Report::updateOrCreate(
                [
                    'user_id'    => $createdUsers[2]->id, // Sofía reporta
                    'artwork_id' => $createdArtworks[1]->id, // Segunda obra de Elena
                ],
                [
                    'reason'     => 'Broken or inaccessible external link',
                    'details'    => 'The 3D model takes too long to respond from the remote host. Suggesting verification.',
                    'status'     => 'pending',
                ]
            );
        }
    }
}

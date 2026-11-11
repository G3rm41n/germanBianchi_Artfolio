<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed realistic multidisciplinary demo portfolio data in English.
     * Fully idempotent: removes legacy/duplicate demo records before seeding.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ─────────────────────────────────────────────────────────────────────
        // 1. DEMO ARTISTS DATA (12 MULTIDISCIPLINARY CREATORS)
        // ─────────────────────────────────────────────────────────────────────

        $artistsData = [
            // ── 1. Elena Rostova (3D & Digital Sculptor) ────────────────────
            [
                'name'              => 'Elena Rostova',
                'email'             => 'elena@artfolio.test',
                'slug'              => 'elena_rostova',
                'bio'               => 'Digital sculptor and 3D artist specializing in anatomical reinterpretations, classical Hellenic statuary, and heroic fantasy props for video games.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
                'crop_x'            => 0,
                'crop_y'            => 0,
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
                        'external_url'  => 'https://sketchfab.com/3d-models/bronze-relief-asset',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c',
                        'embed_html'    => '<iframe title="Bronze Relief" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Valkyrie Ceremonial Helmet',
                        'description'   => 'PBR asset optimized for real-time cinematic sequences. Features etched Nordic rune patterns and worn leather straps.',
                        'price_hint'    => 'Ref: $150 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/valkyrie-helmet-asset',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401',
                        'embed_html'    => '<iframe title="Valkyrie Helmet" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 2. Marcus Vance (Concept Art & Matte Painting) ──────────────
            [
                'name'              => 'Marcus Vance',
                'email'             => 'marcus@artfolio.test',
                'slug'              => 'marcus_vance',
                'bio'               => 'Senior concept artist and matte painter. Creating vast sci-fi horizons, brutalist megastructures, and contemplative moody landscapes.',
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Epic Vistas & Worldbuilding',
                        'description' => 'Atmospheric concept illustrations exploring futuristic outposts and mythical environments.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Temple of Celestial Nebulas',
                        'description'   => 'Matte painting and digital illustration of an ancient observatory nestled atop a sea of cosmic clouds.',
                        'price_hint'    => 'Original prints: $80 USD',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Temple-Of-Celestial-Nebulas-991203',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Knight of the Setting Sun',
                        'description'   => 'Heroic character silhouette against a burning dusk horizon. Color temperature and atmospheric perspective study.',
                        'price_hint'    => 'Personal portfolio showcase',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Knight-Of-Setting-Sun-882194',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'The Outpost at Obsidian Ridge',
                        'description'   => 'Hard-surface sci-fi research facility surrounded by harsh volcanic terrain and geothermal steam.',
                        'price_hint'    => 'Commercial licensing available',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Obsidian-Ridge-Outpost-771822',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 3. Sofía Alarcón (Filmmaking & Animation) ───────────────────
            [
                'name'              => 'Sofía Alarcón',
                'email'             => 'sofia@artfolio.test',
                'slug'              => 'sofia_alarcon',
                'bio'               => 'Independent director and 3D animator focused on emotional storytelling, cinematic lighting, and open-source rendering pipelines.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.1,
                'collections'       => [
                    [
                        'name'        => 'Cinematic Shorts & Open Films',
                        'description' => 'Narrative film productions and technical animation projects rendered in Blender.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Sintel - The Open Animation Movie',
                        'description'   => 'Acclaimed open-source fantasy short film produced by the Blender Foundation. Story of grief, companionship, and vengeance.',
                        'price_hint'    => 'Creative Commons Attribution',
                        'external_url'  => 'https://www.youtube.com/watch?v=eRsGyueVLvQ',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1478760329108-5c3ed9d495a0',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/eRsGyueVLvQ" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Big Buck Bunny - Animated Classic',
                        'description'   => 'Humorous comedy short featuring stylized furry creatures and slapstick woodland dynamics.',
                        'price_hint'    => 'Open Film Archive',
                        'external_url'  => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/YE7VzlLtp-4" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Tears of Steel - Sci-Fi VFX Showcase',
                        'description'   => 'Post-apocalyptic science fiction short combining live-action cinematography with full VFX CGI integration.',
                        'price_hint'    => 'Open Visual Effects Archive',
                        'external_url'  => 'https://www.youtube.com/watch?v=R6MlUcmOul8',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/R6MlUcmOul8" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 4. Lucas Morel (Sound Design & Scoring) ─────────────────────
            [
                'name'              => 'Lucas Morel',
                'email'             => 'lucas@artfolio.test',
                'slug'              => 'lucas_morel',
                'bio'               => 'Composer and acoustic designer crafting atmospheric soundscapes, interactive orchestral scores, and evocative instrumental pieces.',
                'commission_status' => 'closed',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Acoustic Soundscapes & OST',
                        'description' => 'Original orchestral compositions, ambient motifs, and classical chamber pieces.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Symphony No. 5 in C Minor - Masterpiece',
                        'description'   => 'Orchestral performance exploring dramatic dynamics, motivic development, and emotional tension.',
                        'price_hint'    => 'Licensing on request',
                        'external_url'  => 'https://open.spotify.com/track/1234567890abcdef',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/2WfaOiMkCvy7F5fcp2Pxj8?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Clair de Lune - Nocturnal Impression',
                        'description'   => 'Impressionistic piano solo capturing moonlight over still waters with delicate arpeggios.',
                        'price_hint'    => 'Public performance repertoire',
                        'external_url'  => 'https://open.spotify.com/track/0987654321fedcba',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1507838153414-b4b713384a76',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/6NleEiK2585LR9GYI7um28?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Echoes in the Starlit Forest',
                        'description'   => 'Hybrid orchestral cue blending acoustic woodwinds with modular analog synthesizers for a fantasy adventure title.',
                        'price_hint'    => 'Indie game royalty-free license: $140 USD',
                        'external_url'  => 'https://open.spotify.com/track/5544332211aabbcc',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1445985543469-221e57446677',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/2WfaOiMkCvy7F5fcp2Pxj8?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 5. Maya Chen (Narrative Design & Worldbuilding) ─────────────
            [
                'name'              => 'Maya Chen',
                'email'             => 'maya@artfolio.test',
                'slug'              => 'maya_chen',
                'bio'               => 'Lead game writer and narrative architect. Specializing in intricate lore bibles, nonlinear branching systems, and philosophical worldbuilding.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.05,
                'collections'       => [
                    [
                        'name'        => 'Game Design & World Lore Bibles',
                        'description' => 'Comprehensive design documentation, interactive dialogue trees, and cosmological frameworks.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Aetheria: Comprehensive Game Design Document',
                        'description'   => 'Core design bible covering magic progression, faction reputation matrices, and economy balancing.',
                        'price_hint'    => 'Consulting & Design: $60/hr',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Interactive Narrative & Branching Dialogue Guide',
                        'description'   => 'Methodology document detailing node architecture, emotional state flags, and narrative pacing in RPGs.',
                        'price_hint'    => 'Open Academic Resource',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'The Chrono-Weaver: World Lore Compendium',
                        'description'   => 'A 60-page narrative compendium charting seven eras of civilization, forgotten pantheons, and ancient scripts.',
                        'price_hint'    => 'Reference lore dossier',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 6. Daniel Kim (Pixel Art & Retro Assets) ────────────────────
            [
                'name'              => 'Daniel Kim',
                'email'             => 'daniel@artfolio.test',
                'slug'              => 'daniel_kim',
                'bio'               => 'Pixel artist and 2D animator passionate about 16-bit aesthetics, isometric cyber-cities, and dynamic combat sprite sheets.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Neo-Seoul: 16-Bit Cyberpunk',
                        'description' => 'Detailed pixel art scenes and sprite packs inspired by neon lights and retro sci-fi.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Rainy Alleyway - Neo-Seoul 2099',
                        'description'   => 'Hand-crafted 320x180 canvas scaled to high definition. Features animated rain puddle reflections and flickering neon signs.',
                        'price_hint'    => 'Commercial sprite license: $75 USD',
                        'external_url'  => 'https://www.deviantart.com/danielkim/art/Neo-Seoul-Alley-912837',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Mecha Pilot Combat Sprite Sheet',
                        'description'   => 'Complete 8-direction character sprite sheet with idle, dash, combo slash, and jump animations.',
                        'price_hint'    => 'Game asset pack: $45 USD',
                        'external_url'  => 'https://www.deviantart.com/danielkim/art/Mecha-Pilot-Sprites-882736',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Midnight Ramen Stand - Pixel Diorama',
                        'description'   => 'Isometric cozy nighttime food cart with steam particle aesthetics and warm amber illumination.',
                        'price_hint'    => 'Print & Desktop Wallpaper pack',
                        'external_url'  => 'https://www.deviantart.com/danielkim/art/Midnight-Ramen-Stand-924810',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1563089145-599997674d42',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 7. Chloe Dubois (Stylized & Hand-Painted 3D) ────────────────
            [
                'name'              => 'Chloe Dubois',
                'email'             => 'chloe@artfolio.test',
                'slug'              => 'chloe_dubois',
                'bio'               => '3D environment artist crafting cozy, hand-painted worlds, whimsical cottages, and stylized fantasy foliage.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.15,
                'collections'       => [
                    [
                        'name'        => 'Cozy Forest Dioramas',
                        'description' => 'Low-poly stylized modular environments with hand-painted gradient diffuse textures.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Herbalist Cozy Cottage Diorama',
                        'description'   => 'Stylized 3D diorama rendered in real-time. Features smoking stone chimneys, mossy shingles, and glowing potion bottles.',
                        'price_hint'    => 'Asset bundle: $65 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/cozy-cottage-diorama',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => '<iframe title="Herbalist Cottage" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Enchanted Autumn Windmill',
                        'description'   => 'Low-poly asset with animated cloth sails and floating autumn leaves. Designed for mobile fantasy titles.',
                        'price_hint'    => 'Ref: $40 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/autumn-windmill-asset',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05',
                        'embed_html'    => '<iframe title="Autumn Windmill" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Crystal Cavern & Glowing Flora Pack',
                        'description'   => 'Modular underground biome set with emissive crystal formations, mushrooms, and stone stalagmites.',
                        'price_hint'    => 'Full pack: $85 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/crystal-cavern-pack',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5',
                        'embed_html'    => '<iframe title="Crystal Cavern" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 8. Gabriel Santana (Motion Graphics & Simulation) ───────────
            [
                'name'              => 'Gabriel Santana',
                'email'             => 'gabriel@artfolio.test',
                'slug'              => 'gabriel_santana',
                'bio'               => 'Motion designer and technical animator experimenting with procedural particle physics, liquid simulations, and typography.',
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Kinetic Motion & Visual Experiments',
                        'description' => 'Dynamic particle simulations, procedural character rigs, and abstract title sequences.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Spring - Blender Open Movie Project',
                        'description'   => 'Poetic visual short film showcasing next-generation character hair simulations and expressive creature acting.',
                        'price_hint'    => 'Open Source Film Showcase',
                        'external_url'  => 'https://www.youtube.com/watch?v=WhWc3b3KhnY',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4',
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
                    [
                        'title'         => 'Charge - Open Source Real-Time Action',
                        'description'   => 'High-octane robotic combat sequence rendered in real-time using modern physics and volumetric lighting.',
                        'price_hint'    => 'Motion reel showcase',
                        'external_url'  => 'https://www.youtube.com/watch?v=100_q_e466U',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/100_q_e466U" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 9. Aria Thorne (Electronic Synthesis & Soundtracks) ─────────
            [
                'name'              => 'Aria Thorne',
                'email'             => 'aria@artfolio.test',
                'slug'              => 'aria_thorne',
                'bio'               => 'Electronic music producer and Foley artist designing futuristic synthwave, mechanical sound effects, and sci-fi ambient atmospheres.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.1,
                'collections'       => [
                    [
                        'name'        => 'Cyberpunk Synthesis & Darkwave',
                        'description' => 'Heavy analog basslines, shimmering arpeggios, and dystopian cinematic themes.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Neon Overdrive - Cyberpunk Driving Track',
                        'description'   => 'High-tempo synthwave anthem built around vintage analog synthesizers, punchy gated snares, and sweeping filters.',
                        'price_hint'    => 'Commercial sync license: $160 USD',
                        'external_url'  => 'https://open.spotify.com/track/4uLU6hMCjMI75M1A2tKUQC',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/4uLU6hMCjMI75M1A2tKUQC?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Subterranean Pulse - Industrial Dark Ambient',
                        'description'   => 'Deep rumbling drones, metallic industrial textures, and reverb-drenched claustrophobic sound design.',
                        'price_hint'    => 'Horror & thriller game OST',
                        'external_url'  => 'https://open.spotify.com/track/7qiZfU4dY1lWllzX7mPBI3',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/7qiZfU4dY1lWllzX7mPBI3?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Valkyrie Protocol - High-Energy Synth OST',
                        'description'   => 'Dynamic combat track with fast-paced rhythmic sequencers and vocal chop leads for cybernetic boss encounters.',
                        'price_hint'    => 'Game audio license: $120 USD',
                        'external_url'  => 'https://open.spotify.com/track/2WfaOiMkCvy7F5fcp2Pxj8',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1507838153414-b4b713384a76',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/2WfaOiMkCvy7F5fcp2Pxj8?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 10. Tariq Al-Mansoor (ArchViz & Parametric 3D) ──────────────
            [
                'name'              => 'Tariq Al-Mansoor',
                'email'             => 'tariq@artfolio.test',
                'slug'              => 'tariq_almansoor',
                'bio'               => 'Architectural visualizer and 3D environment designer exploring parametric structures, Islamic geometry, and photorealistic lighting in Unreal Engine.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Parametric Sanctuaries & ArchViz',
                        'description' => 'Real-time architectural walk-throughs, lighting studies, and sacred geometry pavilions.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Courtyard of Pillars - Real-Time ArchViz',
                        'description'   => 'Colonnade pavilion featuring intricate Islamic tile patterns, shallow reflection pools, and realistic physically-based sunlight.',
                        'price_hint'    => 'Architectural visualization: $500 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/courtyard-pillars-archviz',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f',
                        'embed_html'    => '<iframe title="Courtyard Pillars" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Desert Oasis Pavilion at Twilight',
                        'description'   => 'Digital architectural matte painting of an ultra-modern desert oasis residence glowing under the dusk stars.',
                        'price_hint'    => 'Print edition: $90 USD',
                        'external_url'  => 'https://www.deviantart.com/tariqalmansoor/art/Oasis-Pavilion-Twilight-921822',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Floating Geodesic Sanctuary',
                        'description'   => 'Futuristic glass-dome biosphere asset designed for virtual reality and metaverse architectural installations.',
                        'price_hint'    => 'VR ready asset: $175 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/geodesic-sanctuary',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab',
                        'embed_html'    => '<iframe title="Geodesic Sanctuary" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/7w7pAfrCvuqPfQLPCh37mT/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 11. Sarah Jenkins (Technical Narrative & Systems Writing) ───
            [
                'name'              => 'Sarah Jenkins',
                'email'             => 'sarah@artfolio.test',
                'slug'              => 'sarah_jenkins',
                'bio'               => 'Narrative designer and technical writer producing game systems specifications, tabletop RPG campaign settings, and faction lore.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Tabletop RPG Systems & Campaign Modules',
                        'description' => 'System balance sheets, faction allegiances, and full-length pen & paper adventure modules.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Project Andromeda: Technical Design Document',
                        'description'   => 'Comprehensive 45-page architectural specification detailing space combat physics, economy faucets, and player progression.',
                        'price_hint'    => 'Studio Technical Consulting: $75/hr',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Procedural Faction Reputation Mechanics',
                        'description'   => 'Algorithmic whitepaper on multi-tier faction relationships, betrayal triggers, and dynamic trade routes.',
                        'price_hint'    => 'Open Industry Whitepaper',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Legends of the Iron Coast - Campaign Module',
                        'description'   => 'Complete 120-page tabletop adventure containing encounter tables, custom stat blocks, and player hand-outs.',
                        'price_hint'    => 'Commercial PDF: $25 USD',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
                        'provider'      => 'gdocs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/e/2PACX-1vT8x3kUo_placeholder/pub?embedded=true" class="w-full h-96 rounded-lg border"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 12. Kenji Takahashi (Anime Visual Development & Animation) ───
            [
                'name'              => 'Kenji Takahashi',
                'email'             => 'kenji@artfolio.test',
                'slug'              => 'kenji_takahashi',
                'bio'               => 'Visual development artist and traditional 2D animator creating painted background scenery and hand-drawn character studies.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.05,
                'collections'       => [
                    [
                        'name'        => 'Sunlit Nostalgia & Anime Matte Scenery',
                        'description' => 'Painted background scenery evoking summer clouds, rural trains, and golden afternoon warmth.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Old Shrine at Golden Hour',
                        'description'   => 'Hand-painted background environment featuring overgrown stone torii gates, rustling bamboo, and warm sunbeams.',
                        'price_hint'    => 'Original prints: $65 USD',
                        'external_url'  => 'https://www.deviantart.com/kenjitakahashi/art/Old-Shrine-Golden-Hour-931024',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Breeze Along the Coastal Railway',
                        'description'   => 'Lush scenic painting of a seaside train stop under towering cumulus clouds on a midsummer afternoon.',
                        'price_hint'    => 'Visual development sample',
                        'external_url'  => 'https://www.deviantart.com/kenjitakahashi/art/Coastal-Railway-Breeze-881923',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Summer Cicadas - Hand-Drawn 2D Animation Reel',
                        'description'   => 'Showcase of rough pencil tests and polished cleanup animation exploring subtle human emotions and body weight.',
                        'price_hint'    => 'Animation portfolio reel',
                        'external_url'  => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/aqz-KE-bpKQ" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],
        ];

        // ─────────────────────────────────────────────────────────────────────
        // 2. CLEANUP LEGACY DEMO CONTENT (Removes old Spanish entries & duplicates)
        // ─────────────────────────────────────────────────────────────────────

        $demoEmails = array_column($artistsData, 'email');
        $existingDemoUsers = User::whereIn('email', $demoEmails)->get();

        foreach ($existingDemoUsers as $existingUser) {
            Bookmark::where('user_id', $existingUser->id)->delete();
            Report::whereHas('artwork', fn($q) => $q->where('user_id', $existingUser->id))->delete();
            $existingUser->artworks()->forceDelete();
            $existingUser->collections()->delete();
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. PERSIST CREATORS, COLLECTIONS & ARTWORKS
        // ─────────────────────────────────────────────────────────────────────

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
                    'email_verified_at' => now(),
                ]
            );

            $createdUsers[] = $user;

            // Seed collections
            $createdCollections = [];
            foreach ($artistInfo['collections'] as $colData) {
                $collection = Collection::firstOrCreate([
                    'user_id' => $user->id,
                    'name'    => $colData['name'],
                ], [
                    'description' => $colData['description'],
                ]);
                $createdCollections[] = $collection;
            }

            // Seed artworks
            foreach ($artistInfo['artworks'] as $artData) {
                $artwork = Artwork::create([
                    'user_id'       => $user->id,
                    'title'         => $artData['title'],
                    'description'   => $artData['description'],
                    'price_hint'    => $artData['price_hint'],
                    'external_url'  => $artData['external_url'],
                    'provider'      => $artData['provider'],
                    'category'      => $artData['category'],
                    'render_type'   => $artData['render_type'],
                    'thumbnail_url' => $artData['thumbnail_url'],
                    'embed_html'    => $artData['embed_html'],
                    'is_featured'   => $artData['is_featured'],
                    'status'        => 'published',
                ]);

                // Attach to creator's collection if available
                if (!empty($createdCollections)) {
                    $artwork->collections()->syncWithoutDetaching([$createdCollections[0]->id]);
                }

                $createdArtworks[] = $artwork;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. CROSS-BOOKMARKS (Favoriting other creators' works)
        // ─────────────────────────────────────────────────────────────────────

        if (count($createdUsers) >= 8 && count($createdArtworks) >= 16) {
            // Elena (3D) bookmarks Marcus's concept art and Sofia's video
            Bookmark::updateOrCreate(['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[3]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[6]->id]);

            // Marcus (Image) bookmarks Elena's 3D sculpture and Daniel's pixel art
            Bookmark::updateOrCreate(['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[0]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[15]->id]);

            // Sofia (Video) bookmarks Lucas's music and Gabriel's motion graphics
            Bookmark::updateOrCreate(['user_id' => $createdUsers[2]->id, 'artwork_id' => $createdArtworks[9]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[2]->id, 'artwork_id' => $createdArtworks[21]->id]);

            // Lucas (Audio) bookmarks Maya's design doc and Aria's synth track
            Bookmark::updateOrCreate(['user_id' => $createdUsers[3]->id, 'artwork_id' => $createdArtworks[12]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[3]->id, 'artwork_id' => $createdArtworks[24]->id]);

            // Maya (Doc) bookmarks Tariq's ArchViz and Sarah's RPG guide
            Bookmark::updateOrCreate(['user_id' => $createdUsers[4]->id, 'artwork_id' => $createdArtworks[27]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[4]->id, 'artwork_id' => $createdArtworks[30]->id]);

            // Daniel (Pixel Art) bookmarks Kenji's anime scenery and Chloe's 3D diorama
            Bookmark::updateOrCreate(['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[33]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[18]->id]);

            // Chloe (3D) bookmarks Elena's bust and Kenji's coastal railway
            Bookmark::updateOrCreate(['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[0]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[34]->id]);

            // Gabriel (Motion) bookmarks Sofia's open movie and Marcus's outpost
            Bookmark::updateOrCreate(['user_id' => $createdUsers[7]->id, 'artwork_id' => $createdArtworks[6]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[7]->id, 'artwork_id' => $createdArtworks[5]->id]);
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. SAMPLE MODERATION REPORT
        // ─────────────────────────────────────────────────────────────────────

        if (count($createdUsers) >= 2 && count($createdArtworks) >= 2) {
            Report::updateOrCreate(
                [
                    'user_id'    => $createdUsers[1]->id, // Marcus reports
                    'artwork_id' => $createdArtworks[1]->id, // Elena's second artwork
                ],
                [
                    'reason'     => 'Broken or inaccessible external link',
                    'details'    => 'The 3D model asset took longer than expected to load from the remote host. Requesting verification.',
                    'status'     => 'pending',
                ]
            );
        }
    }
}

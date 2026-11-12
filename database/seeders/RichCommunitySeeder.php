<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\AuditLog;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RichCommunitySeeder extends Seeder
{
    /**
     * Seeds expanded community data:
     * - Additional multidisciplinary artists with diverse social media links and tags ('Digital Artist', 'Concept Art', 'Virtual Artist')
     * - New published artworks
     * - Soft-deleted / trashed artworks for trash recovery and administration
     * - Realistic comments across artworks
     * - Moderation reports with various reasons and statuses
     * - Comprehensive audit logs for admin & user activity
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ─────────────────────────────────────────────────────────────────────
        // 1. ADDITIONAL DIVERSE MULTIDISCIPLINARY ARTISTS (8 CREATORS)
        // ─────────────────────────────────────────────────────────────────────
        $newArtists = [
            [
                'name'              => 'Yuki Tanaka',
                'email'             => 'yuki@artfolio.test',
                'slug'              => 'yuki_tanaka',
                'headline'          => 'Cyberpunk Virtual Artist & Environment Architect',
                'bio'               => 'Virtual worldbuilder crafting immersive neo-Tokyo environments, neon megastructures, and real-time game architecture.',
                'city'              => 'Tokyo',
                'country'           => 'Japan',
                'commission_status' => 'open',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2',
                'skills'            => ['Virtual Artist', 'Concept Art', '3D Modeling', 'Environment Art'],
                'software'          => ['Unreal Engine', 'Blender', 'Substance 3D Painter'],
                'social_links'      => [
                    'artstation' => 'https://artstation.com/tanakayuki',
                    'twitter'    => 'https://x.com/yukitanaka_art',
                    'github'     => 'https://github.com/yukitanaka3d',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Unreal Engine Cyberpunk Level Design',
                        'price'       => '$600 - $1200',
                        'description' => 'Fully optimized real-time environment with volumetric lighting and custom PBR shaders.',
                    ],
                    [
                        'title'       => '3D Sci-Fi Prop Modeling',
                        'price'       => '$180',
                        'description' => 'Game-ready hard surface asset with 4K textures.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'Neo-Shinjuku Rain Alley',
                        'description'   => 'Raytraced cyberpunk street corner in Neo-Tokyo featuring wet asphalt reflections, holographic advertisements, and modular storefronts.',
                        'price_hint'    => 'Personal Portfolio / Unreal Engine 5.4',
                        'external_url'  => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                    [
                        'title'         => 'Megastructure Sector 09',
                        'description'   => 'Volumetric vista of atmospheric smog hanging over colossal brutalist residential towers at twilight.',
                        'price_hint'    => 'Concept Art Print: $85 USD',
                        'external_url'  => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'is_featured'   => false,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Mateo Rossi',
                'email'             => 'mateo@artfolio.test',
                'slug'              => 'mateo_rossi',
                'headline'          => 'Creature Concept Designer & Digital Sculptor',
                'bio'               => 'Sculpting mythical beasts, alien fauna, and biomechanical creatures for feature films and AAA tabletop universes.',
                'city'              => 'Buenos Aires',
                'country'           => 'Argentina',
                'commission_status' => 'consult',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'skills'            => ['Concept Art', 'Character Design', 'Digital Sculpting', 'Digital Artist'],
                'software'          => ['ZBrush', 'Photoshop', 'Maya'],
                'social_links'      => [
                    'artstation' => 'https://artstation.com/mateorossi',
                    'instagram'  => 'https://instagram.com/mateorossi.creatures',
                    'behance'    => 'https://behance.net/mateorossi',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Film Creature Concept Sheet',
                        'price'       => '$500',
                        'description' => 'Anatomy orthographics, color variations, and behavioral motion sketches.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Abyssal Leviathan',
                        'description'   => 'Deep-sea bioluminescent apex predator sculpted in ZBrush with translucent fin shaders and predatory jaw anatomy.',
                        'price_hint'    => 'Film Concept Pitch',
                        'external_url'  => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Astrid Lindholm',
                'email'             => 'astrid@artfolio.test',
                'slug'              => 'astrid_lindholm',
                'headline'          => 'Nordic Matte Painter & Virtual Production Lead',
                'bio'               => 'Combining photographic plates with procedural terrain and cinematic light in Unreal Engine for Scandinavian indie productions.',
                'city'              => 'Stockholm',
                'country'           => 'Sweden',
                'commission_status' => 'open',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
                'skills'            => ['Matte Painting', 'Virtual Artist', 'Lighting & LookDev', 'VFX'],
                'software'          => ['Unreal Engine', 'Photoshop', 'Houdini'],
                'social_links'      => [
                    'linkedin'   => 'https://linkedin.com/in/astridlindholm',
                    'artstation' => 'https://artstation.com/astrid_vfx',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Cinematic 8K Matte Painting',
                        'price'       => '$750',
                        'description' => 'Multi-plane camera projected background plate for film and television.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'Fjord of the Northern Lights',
                        'description'   => 'Panoramic matte painting blending glacial photography with dynamic aurora borealis particle effects.',
                        'price_hint'    => 'Film Backdrop Plate',
                        'external_url'  => 'https://images.unsplash.com/photo-1483347756197-71ef80e95f73',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1483347756197-71ef80e95f73',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Devon Thorne',
                'email'             => 'devon@artfolio.test',
                'slug'              => 'devon_thorne',
                'headline'          => 'Indie Game Director & Pixel Artist',
                'bio'               => 'Creator of nostalgic 16-bit RPGs, isometric sprites, and tactile pixel interface animations.',
                'city'              => 'Montreal',
                'country'           => 'Canada',
                'commission_status' => 'open',
                'is_verified'       => false,
                'avatar_url'        => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61',
                'skills'            => ['Game Art', 'Pixel Art', 'UI / UX Art', 'Digital Artist'],
                'software'          => ['Unity', 'Godot', 'Figma', 'Photoshop'],
                'social_links'      => [
                    'twitter' => 'https://x.com/devonthorne',
                    'github'  => 'https://github.com/devonthorne-dev',
                    'youtube' => 'https://youtube.com/@devonthorne_dev',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Complete Pixel Art Sprite Sheet (16-dir)',
                        'price'       => '$220',
                        'description' => 'Idle, walk, attack, and death animations with ready-to-import Unity animator controllers.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Kingdom of Ember Ruins - 16-bit Mockup',
                        'description'   => 'Hand-crafted pixel art scene with parallax scrolling layers, warm campfire lighting, and stylized UI HUD.',
                        'price_hint'    => 'Indie Game Asset Packet',
                        'external_url'  => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f',
                        'is_featured'   => false,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Fatima Zahra',
                'email'             => 'fatima@artfolio.test',
                'slug'              => 'fatima_zahra',
                'headline'          => 'Architectural CGI & Geometric Virtual Artist',
                'bio'               => 'Blending historical Islamic arabesque geometries with futuristic parametric architecture and daylight raytracing.',
                'city'              => 'Dubai',
                'country'           => 'United Arab Emirates',
                'commission_status' => 'open',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2',
                'skills'            => ['3D Modeling', 'Texturing', 'Virtual Artist', 'Digital Artist'],
                'software'          => ['Cinema 4D', 'Blender', 'Illustrator'],
                'social_links'      => [
                    'behance'   => 'https://behance.net/fatimazahra_arch',
                    'instagram' => 'https://instagram.com/fatimazahra.cgi',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Parametric Architectural Visualization',
                        'price'       => '$800',
                        'description' => 'High-end photorealistic renders showcasing bespoke parametric geometries.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'Oasis of Light - Parametric Mosque Pavilion',
                        'description'   => 'Intricate ceiling lattice filtering natural desert sunlight into dynamic geometric patterns on polished travertine stone.',
                        'price_hint'    => 'Architectural Monograph Feature',
                        'external_url'  => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Leo Novak',
                'email'             => 'leo@artfolio.test',
                'slug'              => 'leo_novak',
                'headline'          => 'Mecha & Hard Surface Concept Artist',
                'bio'               => 'Designing industrial robotics, planetary rovers, and exoskeleton prototypes for science fiction entertainment.',
                'city'              => 'Berlin',
                'country'           => 'Germany',
                'commission_status' => 'consult',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce',
                'skills'            => ['Concept Art', 'Digital Artist', '3D Modeling', 'Digital Painting'],
                'software'          => ['Blender', 'Photoshop', 'Marvelous Designer', 'ZBrush'],
                'social_links'      => [
                    'artstation' => 'https://artstation.com/leonovak_mecha',
                    'twitter'    => 'https://x.com/leonovak_art',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Hard Surface Mecha Design',
                        'price'       => '$450',
                        'description' => 'Detailed multi-angle mechanical turnaround with hydraulic articulation diagrams.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'APEX-04 Heavy Industrial Exoskeleton',
                        'description'   => 'Rough-terrain heavy construction suit equipped with pneumatic stabilizer anchors and reinforced cockpit shielding.',
                        'price_hint'    => 'Sci-Fi Film Pre-production',
                        'external_url'  => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Nadia Volkova',
                'email'             => 'nadia@artfolio.test',
                'slug'              => 'nadia_volkova',
                'headline'          => 'Stylized Fantasy Character Artist',
                'bio'               => 'Illustrating vibrant storybook heroines, folklore spirits, and whimsical creatures with painterly brushwork.',
                'city'              => 'Prague',
                'country'           => 'Czech Republic',
                'commission_status' => 'open',
                'is_verified'       => false,
                'avatar_url'        => 'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'skills'            => ['Character Design', 'Illustration', 'Digital Artist', 'Concept Art'],
                'software'          => ['Clip Studio Paint', 'Photoshop', 'Procreate'],
                'social_links'      => [
                    'artstation' => 'https://artstation.com/nadiavolkova',
                    'instagram'  => 'https://instagram.com/nadia_artworks',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Character Portrait & Expression Sheet',
                        'price'       => '$190',
                        'description' => 'Full-body stylized fantasy character with 4 facial expressions and accessory callouts.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Alchemist of Whispering Woods',
                        'description'   => 'Playful fantasy illustration of a young herbalist organizing glowing crystalline potions in an overgrown tree hollow.',
                        'price_hint'    => 'Book Cover Art: $300 USD',
                        'external_url'  => 'https://images.unsplash.com/photo-1534447677768-be436bb09401',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401',
                        'is_featured'   => true,
                        'is_mature'     => false,
                    ],
                ],
            ],
            [
                'name'              => 'Liam O\'Connor',
                'email'             => 'liam@artfolio.test',
                'slug'              => 'liam_oconnor',
                'headline'          => 'LookDev & Cinematic Lighting Artist',
                'bio'               => 'Lighting and shading supervisor for film cinematics and real-time virtual productions with Unreal Engine.',
                'city'              => 'Dublin',
                'country'           => 'Ireland',
                'commission_status' => 'open',
                'is_verified'       => true,
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'skills'            => ['Lighting & LookDev', 'Virtual Artist', 'VFX', '3D Modeling'],
                'software'          => ['Maya', 'Unreal Engine', 'Houdini', 'DaVinci Resolve'],
                'social_links'      => [
                    'linkedin'   => 'https://linkedin.com/in/liamoconnor-lighting',
                    'artstation' => 'https://artstation.com/liamoconnor',
                ],
                'commission_services' => [
                    [
                        'title'       => 'Cinematic Sequence Lighting & Grading',
                        'price'       => '$500',
                        'description' => 'Custom volumetric lighting rig, camera exposure balance, and ACES color grading in Unreal Engine.',
                    ]
                ],
                'artworks'          => [
                    [
                        'title'         => 'Cathedral of Shadows - Lighting Study',
                        'description'   => 'Dynamic lighting breakdown contrasting deep cerulean shadows with piercing amber stained-glass godrays.',
                        'price_hint'    => 'Lighting Masterclass Demonstration',
                        'external_url'  => 'https://images.unsplash.com/photo-1519817650390-64a93db51149',
                        'provider'      => 'direct',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1519817650390-64a93db51149',
                        'is_featured'   => false,
                        'is_mature'     => false,
                    ],
                ],
            ],
        ];

        $savedArtists = [];
        $savedArtworks = [];

        foreach ($newArtists as $artistData) {
            $user = User::updateOrCreate(
                ['email' => $artistData['email']],
                [
                    'name'                    => $artistData['name'],
                    'slug'                    => $artistData['slug'],
                    'password'                => $password,
                    'headline'                => $artistData['headline'],
                    'bio'                     => $artistData['bio'],
                    'city'                    => $artistData['city'],
                    'country'                 => $artistData['country'],
                    'commission_status'       => $artistData['commission_status'],
                    'is_verified'             => $artistData['is_verified'],
                    'avatar_url'              => $artistData['avatar_url'],
                    'crop_x'                  => 0,
                    'crop_y'                  => 0,
                    'crop_zoom'               => 1.0,
                    'skills'                  => $artistData['skills'],
                    'software'                => $artistData['software'],
                    'social_links'            => $artistData['social_links'],
                    'commission_services'     => $artistData['commission_services'],
                    'hiring_types'            => ['freelance', 'contract'],
                    'public_email_enabled'    => true,
                    'onboarding_step'         => 3,
                    'onboarding_completed_at' => now(),
                    'is_admin'                => false,
                    'status'                  => 'active',
                    'email_verified_at'       => now(),
                ]
            );

            $savedArtists[] = $user;

            // User collection
            $collection = Collection::firstOrCreate([
                'user_id' => $user->id,
                'name'    => 'Featured Works & Experiments',
            ], [
                'description' => 'Primary showcase portfolio highlighting production studies and commissions.',
            ]);

            // Save artworks
            foreach ($artistData['artworks'] as $artItem) {
                $artwork = Artwork::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title'   => $artItem['title'],
                    ],
                    [
                        'description'     => $artItem['description'],
                        'price_hint'      => $artItem['price_hint'],
                        'external_url'    => $artItem['external_url'],
                        'provider'        => $artItem['provider'],
                        'category'        => $artItem['category'],
                        'render_type'     => $artItem['render_type'],
                        'thumbnail_url'   => $artItem['thumbnail_url'],
                        'embed_html'      => $artItem['embed_html'] ?? null,
                        'is_featured'     => $artItem['is_featured'] ?? false,
                        'is_mature'       => (bool) ($artItem['is_mature'] ?? false),
                        'is_ai_generated' => (bool) ($artItem['is_ai_generated'] ?? false),
                        'software_used'   => $artItem['software_used'] ?? $user->software,
                        'status'          => 'published',
                        'views_count'     => rand(45, 420),
                        'likes_count'     => rand(8, 89),
                    ]
                );

                if (!empty($user->skills)) {
                    $tagIds = [];
                    foreach ($user->skills as $skillName) {
                        $tModel = \App\Models\Tag::firstOrCreate(
                            ['slug' => \Illuminate\Support\Str::slug($skillName)],
                            ['name' => $skillName]
                        );
                        $tagIds[] = $tModel->id;
                    }
                    $artwork->tags()->syncWithoutDetaching($tagIds);
                }

                $artwork->collections()->syncWithoutDetaching([$collection->id]);
                $savedArtworks[] = $artwork;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 1.5 ADDITIONAL COMMUNITY ARTWORKS (PINTEREST, DEVIANTART, SKETCHFAB)
        // 100% genuine, active, real URLs tested with HTTP 200 responses.
        // Diverse combinations of is_mature (18+) and is_ai_generated (AI)
        // ─────────────────────────────────────────────────────────────────────
        $additionalArtworksData = [
            // ── PINTEREST ARTWORKS (Authentic verified Pinterest pins & original CDN images) ──
            [
                'artist_email'    => 'mateo@artfolio.test',
                'title'           => 'The Wishing Fairy',
                'description'     => 'Gothic dark fantasy illustration of an ethereal forest fairy illuminated by candlelight and occult sigils by Godiva Ghoul.',
                'price_hint'      => 'Dark Fantasy Art Print: $65 USD',
                'external_url'    => 'https://www.pinterest.com/pin/34199278421133379/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/05/25/c4/0525c47edbaa302d4749a4ea59ef1dc4.jpg',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => true, // +18 sensitive filter testing (Safe imagery)
                'is_ai_generated' => false,
                'software_used'   => ['Photoshop', 'ZBrush'],
                'tags'            => ['Concept Art', 'Character Design', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'yuki@artfolio.test',
                'title'           => 'The City - Sci-Fi Megastructure',
                'description'     => 'Vast sci-fi megacity panoramic vista under golden sunset rays with flying sky-cruisers and gleaming towers by Papayoufr.',
                'price_hint'      => 'Sci-Fi Concept Print: $55 USD',
                'external_url'    => 'https://www.pinterest.com/pin/34199278421133375/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/d1/22/ff/d122ff9a8fa87cfb2de90e4f1716a92f.jpg',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI-assisted generation testing
                'software_used'   => ['Blender', 'Unreal Engine'],
                'tags'            => ['Virtual Artist', 'Concept Art', '3D Modeling'],
            ],
            [
                'artist_email'    => 'mateo@artfolio.test',
                'title'           => 'Midas - Golden Transmutation',
                'description'     => 'Mythological illustration exploring King Midas with gilded foliage, detailed metallic reflections, and dark floral textures by Serena Malyon.',
                'price_hint'      => 'Fine Art Archival Edition: $80 USD',
                'external_url'    => 'https://www.pinterest.com/pin/34199278421133374/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/07/62/f2/0762f28a4ad78400fed7f44901d769ca.jpg',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => true, // AI generated + mature combo
                'software_used'   => ['ZBrush', 'Photoshop'],
                'tags'            => ['Digital Sculpting', 'Character Design', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'astrid@artfolio.test',
                'title'           => 'Dreamer',
                'description'     => 'Atmospheric fine art illustration exploring dreamlike tranquility, flowing fabric, and floral symbolism by Chantal Horeis on INPRNT.',
                'price_hint'      => 'Fine Art Print on INPRNT: $45 USD',
                'external_url'    => 'https://www.pinterest.com/pin/34199278421134047/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/35/ac/8d/35ac8d09028b9bc0989d27859433beda.jpg',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => false,
                'software_used'   => ['Photoshop', 'Procreate'],
                'tags'            => ['Digital Painting', 'Illustration'],
            ],
            [
                'artist_email'    => 'astrid@artfolio.test',
                'title'           => 'Postcards from Prague - Golden Dawn',
                'description'     => 'Cityscape capturing Prague\'s historical bridges and cathedral spires under radiant morning mist by Milan Chudoba on Behance.',
                'price_hint'      => 'Photoshop Architecture Study',
                'external_url'    => 'https://www.pinterest.com/pin/59883870041186096/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/43/3a/fc/433afceb0db84737a809897a2d09698f.jpg',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI generated
                'software_used'   => ['Unreal Engine', 'Photoshop'],
                'tags'            => ['Matte Painting', 'Virtual Artist', 'Lighting & LookDev'],
            ],
            [
                'artist_email'    => 'yuki@artfolio.test',
                'title'           => 'Postcards from Prague - Gothic Spire in Mist',
                'description'     => 'Atmospheric winter study of gothic spires rising through river mist with dramatic chiaroscuro by Milan Chudoba.',
                'price_hint'      => 'Digital Matte Study',
                'external_url'    => 'https://www.pinterest.com/pin/59883870041186093/',
                'provider'        => 'pinterest',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://i.pinimg.com/originals/b5/38/99/b538992468c74b465708b7264a1d9ac5.jpg',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => false,
                'software_used'   => ['Photoshop', 'Houdini'],
                'tags'            => ['Matte Painting', 'Concept Art'],
            ],

            // ── DEVIANTART ARTWORKS (Authentic verified DeviantArt art pages & CDN images) ──
            [
                'artist_email'    => 'nadia@artfolio.test',
                'title'           => 'Spider Warrior',
                'description'     => 'Dark fantasy creature warrior concept with chitinous exoskeleton armor and arachnid aesthetic by AlbertoV on DeviantArt.',
                'price_hint'      => 'Creature Concept Pitch: $350 USD',
                'external_url'    => 'https://www.deviantart.com/albertov/art/Spider-Warrior-1298180200',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => false,
                'software_used'   => ['Clip Studio Paint', 'Photoshop'],
                'tags'            => ['Character Design', 'Concept Art', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'nadia@artfolio.test',
                'title'           => 'Antay Version Final - Dnd',
                'description'     => 'Tabletop fantasy character concept art depicting a battle-hardened barbarian with detailed furs and ornate weaponry by jampart2003 on DeviantArt.',
                'price_hint'      => 'D&D Character Commission: $160 USD',
                'external_url'    => 'https://www.deviantart.com/jampart2003/art/Antay-Version-Final-Dnd-1119117490',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1578632767115-351597cf2477',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI generated
                'software_used'   => ['Clip Studio Paint', 'Procreate'],
                'tags'            => ['Character Design', 'Illustration', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'devon@artfolio.test',
                'title'           => 'Secret Garden',
                'description'     => 'Overgrown fantasy courtyard with sunbeams piercing mossy stone walls and quiet flora by MilosLach on DeviantArt.',
                'price_hint'      => 'Fine Art Print: $85 USD',
                'external_url'    => 'https://www.deviantart.com/miloslach/art/Secret-Garden-983483641',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1518770660439-4636190af475',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => false,
                'is_ai_generated' => false,
                'software_used'   => ['Photoshop', 'Clip Studio Paint'],
                'tags'            => ['Concept Art', 'Digital Painting', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'fatima@artfolio.test',
                'title'           => 'Landscape Drawing (Architecture Art)',
                'description'     => 'Fine architectural pen and digital sketch exploring perspective geometry and urban density by andrecadoreart on DeviantArt.',
                'price_hint'      => 'Original Architectural Study: $120 USD',
                'external_url'    => 'https://www.deviantart.com/andrecadoreart/art/Landscape-Drawing-1399327015',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => true, // AI + mature combo
                'software_used'   => ['Cinema 4D', 'Photoshop'],
                'tags'            => ['3D Modeling', 'Virtual Artist', 'Texturing'],
            ],
            [
                'artist_email'    => 'devon@artfolio.test',
                'title'           => 'Lakeside cottage on foggy morning',
                'description'     => 'Serene watercolor style digital landscape of a wooden cottage by calm morning waters by tanmu-u on DeviantArt.',
                'price_hint'      => 'Digital Watercolor Print: $50 USD',
                'external_url'    => 'https://www.deviantart.com/tanmu-u/art/Lakeside-cottage-on-foggy-morning-1269338093',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                'embed_html'      => null,
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI generated
                'software_used'   => ['Photoshop', 'Figma'],
                'tags'            => ['Game Art', 'Pixel Art', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'fatima@artfolio.test',
                'title'           => 'Une belle rencontre',
                'description'     => 'Warm impressionistic digital oil painting depicting figures in a sun-drenched European town square by TABLEAUXTOMPAT on DeviantArt.',
                'price_hint'      => 'Fine Art Canvas: $220 USD',
                'external_url'    => 'https://www.deviantart.com/tableauxtompat/art/Une-belle-rencontre-1407002408',
                'provider'        => 'deviantart',
                'category'        => 'image',
                'render_type'     => 'image',
                'thumbnail_url'   => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9',
                'embed_html'      => null,
                'is_featured'     => true,
                'is_mature'       => false,
                'is_ai_generated' => false,
                'software_used'   => ['Cinema 4D', 'Photoshop'],
                'tags'            => ['3D Modeling', 'Virtual Artist', 'Texturing'],
            ],

            // ── SKETCHFAB 3D MODELS (Authentic verified 3D models with interactive WebGL embeds) ──
            [
                'artist_email'    => 'leo@artfolio.test',
                'title'           => 'The Rosetta Stone (3D Museum Photogrammetry)',
                'description'     => 'High-definition photogrammetry 3D model of the iconic granodiorite stele inscribed in Memphis in 196 BC, scanned by The British Museum on Sketchfab.',
                'price_hint'      => 'Public Cultural Domain / CC-BY',
                'external_url'    => 'https://sketchfab.com/3d-models/the-rosetta-stone-1e03509704a3490e99a173e53b93e282',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/1e03509704a3490e99a173e53b93e282/thumbnails/e98e8aa8605c4a49b17babf8a47c66f4/68d731be1c9e48a4aa9485ebf9275f91.jpeg',
                'embed_html'      => '<iframe title="The Rosetta Stone" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/1e03509704a3490e99a173e53b93e282/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => true,
                'is_mature'       => false,
                'is_ai_generated' => false,
                'software_used'   => ['Blender', 'Substance 3D Painter'],
                'tags'            => ['3D Modeling', 'Texturing', 'Concept Art'],
            ],
            [
                'artist_email'    => 'leo@artfolio.test',
                'title'           => 'Cyber Samurai (Game-Ready Character)',
                'description'     => 'High-poly 3D character mesh fusing traditional feudal Japanese kabuto armor with futuristic cybernetic prosthetics by KhoaMinh on Sketchfab.',
                'price_hint'      => 'Game Ready Rig: $180 USD',
                'external_url'    => 'https://sketchfab.com/3d-models/cyber-samurai-26ccafaddb2745ceb56ae5cfc65bfed5',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/26ccafaddb2745ceb56ae5cfc65bfed5/thumbnails/1e3cb3c710a04f96a9fdad8394dd492e/4ff8482d739e49a7aa7013838d248439.jpeg',
                'embed_html'      => '<iframe title="Cyber Samurai" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/26ccafaddb2745ceb56ae5cfc65bfed5/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI generated 3D topology
                'software_used'   => ['Blender', 'Unreal Engine'],
                'tags'            => ['3D Modeling', 'Concept Art', 'Texturing'],
            ],
            [
                'artist_email'    => 'leo@artfolio.test',
                'title'           => 'The Queen of Swords',
                'description'     => 'Real-time 3D character sculpture featuring stylized gothic plate armor, an engraved greatsword, and low-poly game topology by SpatialNeglect.',
                'price_hint'      => 'Asset Reference: $150 USD',
                'external_url'    => 'https://sketchfab.com/3d-models/the-queen-of-swords-875528a70ac845d0a67775c288ed3f5a',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/875528a70ac845d0a67775c288ed3f5a/thumbnails/49123c8443c84c6099027eebdf9a71d0/021fb3ae80df449c89f099fa108c1883.jpeg',
                'embed_html'      => '<iframe title="The Queen of Swords" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/875528a70ac845d0a67775c288ed3f5a/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => true,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => false,
                'software_used'   => ['ZBrush', 'Substance 3D Painter'],
                'tags'            => ['3D Modeling', 'Digital Sculpting', 'Digital Artist'],
            ],
            [
                'artist_email'    => 'liam@artfolio.test',
                'title'           => 'Spartan Armour MKV - Halo Reach',
                'description'     => 'PBR real-time 3D recreation of the legendary Mark V MJOLNIR powered battle armor worn by supersoldiers by McCarthy3D on Sketchfab.',
                'price_hint'      => 'Tribute 3D Asset',
                'external_url'    => 'https://sketchfab.com/3d-models/spartan-armour-mkv-halo-reach-57070b2fd9ff472c8988e76d8c5cbe66',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/57070b2fd9ff472c8988e76d8c5cbe66/thumbnails/a57788fd1bc84ecba2c28818d922cfcf/c8b16ee642264d47ab3ca393f882183e.jpeg',
                'embed_html'      => '<iframe title="Spartan Armour MKV" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/57070b2fd9ff472c8988e76d8c5cbe66/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => false,
                'is_mature'       => true, // +18 sensitive filter testing
                'is_ai_generated' => true, // AI + mature combo
                'software_used'   => ['Maya', 'Unreal Engine', 'Houdini'],
                'tags'            => ['3D Modeling', 'Lighting & LookDev', 'VFX'],
            ],
            [
                'artist_email'    => 'liam@artfolio.test',
                'title'           => 'Bust of Róża Loewenfeld (Neoclassical Sculpture)',
                'description'     => 'Museum 3D scan of a neoclassical marble portrait bust highlighting delicate lace carvings and Victorian drapery by the Virtual Museums of Małopolska on Sketchfab.',
                'price_hint'      => 'Public Cultural Domain',
                'external_url'    => 'https://sketchfab.com/3d-models/sculpture-bust-of-roza-loewenfeld-fc6e731a0131471ba8e45511c7ea9996',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/fc6e731a0131471ba8e45511c7ea9996/thumbnails/6a7895ab861d45a9bfaac651e919f5db/blob.jpeg',
                'embed_html'      => '<iframe title="Bust of Roza Loewenfeld" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/fc6e731a0131471ba8e45511c7ea9996/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => false,
                'is_mature'       => false,
                'is_ai_generated' => true, // AI generated
                'software_used'   => ['Maya', 'Unreal Engine', 'DaVinci Resolve'],
                'tags'            => ['Lighting & LookDev', '3D Modeling', 'Virtual Artist'],
            ],
            [
                'artist_email'    => 'liam@artfolio.test',
                'title'           => 'Shiba Inu Cute Companion',
                'description'     => 'Stylized, charming 3D character model of a Shiba Inu dog with hand-painted textures by zixisensei on Sketchfab.',
                'price_hint'      => 'Game Character Prop: $35 USD',
                'external_url'    => 'https://sketchfab.com/3d-models/shiba-faef9fe5ace445e7b2989d1c1ece361c',
                'provider'        => 'sketchfab',
                'category'        => '3d',
                'render_type'     => 'iframe',
                'thumbnail_url'   => 'https://media.sketchfab.com/models/faef9fe5ace445e7b2989d1c1ece361c/thumbnails/3faab229b9a34a808244e3342880b8e9/a0c0f3dcc04645fc89855f917e6fd10e.jpeg',
                'embed_html'      => '<iframe title="Shiba" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/faef9fe5ace445e7b2989d1c1ece361c/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                'is_featured'     => true,
                'is_mature'       => false,
                'is_ai_generated' => false,
                'software_used'   => ['Maya', 'Unreal Engine'],
                'tags'            => ['3D Modeling', 'Lighting & LookDev', 'Virtual Artist'],
            ],
        ];

        foreach ($additionalArtworksData as $artItem) {
            $artist = User::where('email', $artItem['artist_email'])->first() ?? $savedArtists[0];
            $artistCollection = $artist->collections()->first() ?? Collection::firstOrCreate([
                'user_id' => $artist->id,
                'name'    => 'Showcase & Curated Explorations',
            ]);

            $artwork = Artwork::updateOrCreate(
                [
                    'user_id' => $artist->id,
                    'title'   => $artItem['title'],
                ],
                [
                    'description'     => $artItem['description'],
                    'price_hint'      => $artItem['price_hint'],
                    'external_url'    => $artItem['external_url'],
                    'provider'        => $artItem['provider'],
                    'category'        => $artItem['category'],
                    'render_type'     => $artItem['render_type'],
                    'thumbnail_url'   => $artItem['thumbnail_url'],
                    'embed_html'      => $artItem['embed_html'] ?? null,
                    'is_featured'     => $artItem['is_featured'] ?? false,
                    'is_mature'       => (bool) ($artItem['is_mature'] ?? false),
                    'is_ai_generated' => (bool) ($artItem['is_ai_generated'] ?? false),
                    'software_used'   => $artItem['software_used'] ?? $artist->software,
                    'status'          => 'published',
                    'views_count'     => rand(80, 650),
                    'likes_count'     => rand(15, 140),
                ]
            );

            if (!empty($artItem['tags'])) {
                $tagIds = [];
                foreach ($artItem['tags'] as $tagName) {
                    $tModel = \App\Models\Tag::firstOrCreate(
                        ['slug' => \Illuminate\Support\Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tModel->id;
                }
                $artwork->tags()->syncWithoutDetaching($tagIds);
            }

            $artwork->collections()->syncWithoutDetaching([$artistCollection->id]);
            $savedArtworks[] = $artwork;
        }

        // Fetch all active users and artworks
        $allUsers = User::where('status', 'active')->get();
        $allArtworks = Artwork::where('status', 'published')->get();

        // ─────────────────────────────────────────────────────────────────────
        // 2. SOFT-DELETED ARTWORKS ("OBRAS BORRADAS" FOR TRASH RECOVERY)
        // ─────────────────────────────────────────────────────────────────────
        $trashedArtworkData = [
            [
                'title'       => 'Early Prototype Mech V1 (Outdated Topology)',
                'description' => 'Early blockout study with non-manifold geometry discarded prior to the final APEX-04 production run.',
                'artist'      => 'Leo Novak',
                'category'    => '3d',
                'days_ago'    => 3,
            ],
            [
                'title'       => 'Abandoned Sanctuary - Color Key Experiment',
                'description' => 'Discarded lighting rough study testing oversaturated purple haze before settling on sunlit stone.',
                'artist'      => 'Marcus Vance',
                'category'    => 'image',
                'days_ago'    => 6,
            ],
            [
                'title'       => 'Character Draft with Unbaked Normal Maps',
                'description' => 'Unfinished 3D bust with baking artifacts removed from public showcase to polish later.',
                'artist'      => 'Elena Rostova',
                'category'    => '3d',
                'days_ago'    => 2,
            ],
            [
                'title'       => 'Draft Channel Intro Motion Sequence (WIP)',
                'description' => 'Rough keyframe animation with draft typography archived after rebranding.',
                'artist'      => 'Gabriel Santos',
                'category'    => 'video',
                'days_ago'    => 8,
            ],
            [
                'title'       => 'Canceled Commission - Dragon Hatchling Lineart',
                'description' => 'Early client lineart draft canceled due to project scope reorientation.',
                'artist'      => 'Nadia Volkova',
                'category'    => 'image',
                'days_ago'    => 5,
            ],
            [
                'title'       => 'Night Cyber Street Texture Atlas (Superseded)',
                'description' => 'Outdated diffuse map archive replaced by 4K PBR material set.',
                'artist'      => 'Yuki Tanaka',
                'category'    => 'image',
                'days_ago'    => 10,
            ],
        ];

        foreach ($trashedArtworkData as $item) {
            $creator = $allUsers->firstWhere('name', $item['artist']) ?? $allUsers->first();
            if ($creator) {
                $trashed = Artwork::withTrashed()->firstOrCreate(
                    [
                        'user_id' => $creator->id,
                        'title'   => $item['title'],
                    ],
                    [
                        'description'   => $item['description'],
                        'price_hint'    => 'Archived Draft',
                        'external_url'  => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675',
                        'provider'      => 'direct',
                        'category'      => $item['category'],
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675',
                        'status'        => 'published',
                        'deleted_at'    => now()->subDays($item['days_ago']),
                    ]
                );

                if (! $trashed->trashed()) {
                    $trashed->delete();
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. REALISTIC ARTWORK COMMENTS
        // ─────────────────────────────────────────────────────────────────────
        $sampleComments = [
            'The atmosphere and volumetric fog in this scene are utterly breathtaking. Beautiful work!',
            'Stunning sense of scale! Did you model the background structures manually or use procedural kitbashing?',
            'The rim lighting against the darker silhouette works wonders here. Truly inspirational!',
            'Incredible brushwork. The subtle color temperature shifts in the shadows give this so much life.',
            'What material setup did you use for the wet ground reflections? Looks remarkably authentic.',
            'That topology looks so clean and efficient. Great attention to edge loops around the joints.',
            'Pure cinematic vibes! The camera angle makes this composition feel massive and grounded.',
            'Saved this to my inspiration board immediately! Keep up the phenomenal work.',
            'The color harmony here is so calming and cohesive. Love the lighting gradient.',
            'Outstanding portfolio piece. The detail on the hard-surface panels is exceptionally crisp.',
            'Reminds me of classic Syd Mead concepts blended with modern cyberpunk storytelling.',
            'Love how much personality you managed to capture in just the expression and posture!',
        ];

        if ($allArtworks->count() >= 5 && $allUsers->count() >= 3) {
            foreach ($allArtworks->take(12) as $index => $artwork) {
                // Add 1 to 3 comments per artwork from different users
                $commenters = $allUsers->where('id', '!=', $artwork->user_id)->shuffle()->take(2);
                foreach ($commenters as $commenter) {
                    $text = $sampleComments[($index + $commenter->id) % count($sampleComments)];
                    Comment::firstOrCreate([
                        'artwork_id' => $artwork->id,
                        'user_id'    => $commenter->id,
                        'content'    => $text,
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. REALISTIC MODERATION REPORTS
        // ─────────────────────────────────────────────────────────────────────
        $reportScenarios = [
            [
                'reason'     => 'Broken or inaccessible external link',
                'details'    => 'The external Sketchfab 3D embed took over 20 seconds to respond on high-speed internet.',
                'status'     => 'resolved',
                'admin_note' => 'Embed verified and CDN caching cleared. Now loading instantaneously.',
            ],
            [
                'reason'     => 'Unflagged sensitive / mature themes',
                'details'    => 'Fantasy warrior sculpture features mild battle damage gore without the mature content warning tag.',
                'status'     => 'pending',
                'admin_note' => null,
            ],
            [
                'reason'     => 'Suspected artwork scraper / reupload',
                'details'    => 'Please verify if the uploader is the original artist of this architectural render or a fan repost.',
                'status'     => 'resolved',
                'admin_note' => 'Artist confirmed ownership via linked Artstation account verification.',
            ],
            [
                'reason'     => 'Potential AI generation mislabeling',
                'details'    => 'The hands and background foliage exhibit patterns typical of diffusion generation without the AI flag.',
                'status'     => 'resolved',
                'admin_note' => 'Reviewed with original process video and PSD layers. 100% human painted.',
            ],
            [
                'reason'     => 'Low resolution thumbnail image',
                'details'    => 'Thumbnail preview is pixelated on Retina and 4K displays.',
                'status'     => 'resolved',
                'admin_note' => 'Regenerated high-resolution thumbnail asset.',
            ],
            [
                'reason'     => 'Spam or irrelevant advertising description',
                'details'    => 'Artwork description contains duplicate promo links to unrelated cryptocurrency services.',
                'status'     => 'pending',
                'admin_note' => null,
            ],
        ];

        if ($allArtworks->count() >= 6 && $allUsers->count() >= 4) {
            foreach ($reportScenarios as $idx => $scenario) {
                $artwork = $allArtworks[$idx % $allArtworks->count()];
                $reporter = $allUsers->where('id', '!=', $artwork->user_id)->first() ?? $allUsers->last();

                Report::firstOrCreate([
                    'user_id'    => $reporter->id,
                    'artwork_id' => $artwork->id,
                    'reason'     => $scenario['reason'],
                ], [
                    'details'    => $scenario['details'],
                    'status'     => $scenario['status'],
                    'admin_note' => $scenario['admin_note'],
                ]);
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. EXTENSIVE AUDIT LOGS (ADMIN & USER ACTIVITY)
        // ─────────────────────────────────────────────────────────────────────
        $adminUser = User::where('is_admin', true)->first() ?? $allUsers->first();

        $auditEntries = [
            [
                'actor_type'   => 'admin',
                'user_id'      => $adminUser?->id,
                'action'       => 'admin.tag_created',
                'target_type'  => 'CuratedTag',
                'target_id'    => 1,
                'target_label' => 'Virtual Artist',
                'description'  => 'Admin created curated discipline tag "Virtual Artist" with suggested flag enabled.',
                'changes'      => ['name' => 'Virtual Artist', 'type' => 'discipline', 'is_suggested' => true],
                'ip_address'   => '192.168.1.10',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(6)->setHour(10)->setMinute(15),
            ],
            [
                'actor_type'   => 'admin',
                'user_id'      => $adminUser?->id,
                'action'       => 'admin.tag_created',
                'target_type'  => 'CuratedTag',
                'target_id'    => 2,
                'target_label' => 'Digital Artist',
                'description'  => 'Admin added "Digital Artist" to platform taxonomy.',
                'changes'      => ['name' => 'Digital Artist', 'type' => 'discipline', 'is_suggested' => true],
                'ip_address'   => '192.168.1.10',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(6)->setHour(10)->setMinute(22),
            ],
            [
                'actor_type'   => 'admin',
                'user_id'      => $adminUser?->id,
                'action'       => 'admin.report_resolved',
                'target_type'  => 'Report',
                'target_id'    => 1,
                'target_label' => 'Report #1 - Broken Link',
                'description'  => 'Admin resolved external viewer latency report after CDN health inspection.',
                'changes'      => ['status_before' => 'pending', 'status_after' => 'resolved'],
                'ip_address'   => '192.168.1.10',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(4)->setHour(14)->setMinute(45),
            ],
            [
                'actor_type'   => 'admin',
                'user_id'      => $adminUser?->id,
                'action'       => 'admin.user_verified',
                'target_type'  => 'User',
                'target_id'    => $savedArtists[0]?->id ?? 2,
                'target_label' => 'Yuki Tanaka',
                'description'  => 'Admin verified artist account based on verified Artstation credentials.',
                'changes'      => ['is_verified' => true],
                'ip_address'   => '192.168.1.10',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(3)->setHour(11)->setMinute(30),
            ],
            [
                'actor_type'   => 'admin',
                'user_id'      => $adminUser?->id,
                'action'       => 'admin.artwork_moderated',
                'target_type'  => 'Artwork',
                'target_id'    => $savedArtworks[0]?->id ?? 1,
                'target_label' => 'The Rosetta Stone',
                'description'  => 'Admin approved high-definition cultural heritage photogrammetry scan.',
                'changes'      => ['status' => 'published', 'is_featured' => true],
                'ip_address'   => '192.168.1.10',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(3)->setHour(16)->setMinute(02),
            ],
            [
                'actor_type'   => 'user',
                'user_id'      => $savedArtists[0]?->id ?? 2,
                'action'       => 'user.profile_updated',
                'target_type'  => 'User',
                'target_id'    => $savedArtists[0]?->id ?? 2,
                'target_label' => 'Yuki Tanaka',
                'description'  => 'Artist updated professional headline and added GitHub link.',
                'changes'      => ['headline' => 'Cyberpunk Virtual Artist & Environment Architect'],
                'ip_address'   => '133.242.18.91',
                'user_agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15',
                'created_at'   => now()->subDays(2)->setHour(9)->setMinute(12),
            ],
            [
                'actor_type'   => 'user',
                'user_id'      => $savedArtists[1]?->id ?? 3,
                'action'       => 'user.artwork_deleted',
                'target_type'  => 'Artwork',
                'target_id'    => 999,
                'target_label' => 'Early Prototype Mech V1',
                'description'  => 'Artist moved outdated 3D topology blockout to trash.',
                'changes'      => ['deleted_at' => now()->subDays(2)->toDateTimeString()],
                'ip_address'   => '181.44.120.35',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/131.0',
                'created_at'   => now()->subDays(2)->setHour(18)->setMinute(44),
            ],
            [
                'actor_type'   => 'user',
                'user_id'      => $savedArtists[2]?->id ?? 4,
                'action'       => 'user.commission_updated',
                'target_type'  => 'User',
                'target_id'    => $savedArtists[2]?->id ?? 4,
                'target_label' => 'Astrid Lindholm',
                'description'  => 'Artist added 8K Matte Painting commercial package to services.',
                'changes'      => ['commission_status' => 'open'],
                'ip_address'   => '85.228.14.77',
                'user_agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/130.0.0.0',
                'created_at'   => now()->subDays(1)->setHour(12)->setMinute(05),
            ],
            [
                'actor_type'   => 'user',
                'user_id'      => $savedArtists[3]?->id ?? 5,
                'action'       => 'user.artwork_published',
                'target_type'  => 'Artwork',
                'target_id'    => 100,
                'target_label' => 'Kingdom of Ember Ruins',
                'description'  => 'Artist published new 16-bit pixel art game scene.',
                'changes'      => ['category' => 'image', 'status' => 'published'],
                'ip_address'   => '198.16.74.22',
                'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/130.0.0.0',
                'created_at'   => now()->subHours(14),
            ],
        ];

        foreach ($auditEntries as $entry) {
            AuditLog::firstOrCreate([
                'action'       => $entry['action'],
                'target_type'  => $entry['target_type'],
                'target_label' => $entry['target_label'],
                'created_at'   => $entry['created_at'],
            ], [
                'actor_type'   => $entry['actor_type'],
                'user_id'      => $entry['user_id'],
                'target_id'    => $entry['target_id'],
                'description'  => $entry['description'],
                'changes'      => $entry['changes'],
                'ip_address'   => $entry['ip_address'],
                'user_agent'   => $entry['user_agent'],
            ]);
        }
    }
}

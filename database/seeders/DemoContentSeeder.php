<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed realistic multidisciplinary demo portfolio data in English.
     * Uses 100% genuine, active internet media links (Sketchfab, DeviantArt, YouTube, Spotify, Google Docs).
     * Fully idempotent: purges obsolete demo records prior to insertion.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ─────────────────────────────────────────────────────────────────────
        // 1. DEMO ARTISTS DATA (12 REALISTIC MULTIDISCIPLINARY CREATORS)
        // ─────────────────────────────────────────────────────────────────────

        $artistsData = [
            // ── 1. Elena Rostova (3D & Digital Archaeology) ─────────────────
            [
                'name'              => 'Elena Rostova',
                'email'             => 'elena@artfolio.test',
                'slug'              => 'elena_rostova',
                'bio'               => 'Digital sculptor and 3D heritage archivist specializing in photogrammetry scans, museum antiquities, and character topology.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.2,
                'collections'       => [
                    [
                        'name'        => 'Digital Archaeology & Antiquities',
                        'description' => 'Interactive 3D museum scans and digital sculptures preserving world cultural heritage.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'The Rosetta Stone',
                        'description'   => 'High-definition photogrammetry 3D model of the iconic granodiorite stele inscribed in Memphis in 196 BC, digitally scanned by The British Museum.',
                        'price_hint'    => 'Public Museum Heritage / CC-BY',
                        'external_url'  => 'https://sketchfab.com/3d-models/the-rosetta-stone-1e03509704a3490e99a173e53b93e282',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/1e03509704a3490e99a173e53b93e282/thumbnails/e98e8aa8605c4a49b17babf8a47c66f4/68d731be1c9e48a4aa9485ebf9275f91.jpeg',
                        'embed_html'    => '<iframe title="The Rosetta Stone" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/1e03509704a3490e99a173e53b93e282/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Bust of Róża Loewenfeld',
                        'description'   => 'Museum 3D scan of a neoclassical marble portrait bust highlighting delicate lace carvings and Victorian drapery by the Virtual Museums of Małopolska.',
                        'price_hint'    => 'Public Cultural Domain',
                        'external_url'  => 'https://sketchfab.com/3d-models/sculpture-bust-of-roza-loewenfeld-fc6e731a0131471ba8e45511c7ea9996',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/fc6e731a0131471ba8e45511c7ea9996/thumbnails/6a7895ab861d45a9bfaac651e919f5db/blob.jpeg',
                        'embed_html'    => '<iframe title="Bust of Roza Loewenfeld" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/fc6e731a0131471ba8e45511c7ea9996/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'The Queen of Swords',
                        'description'   => 'Real-time 3D character sculpture featuring stylized gothic plate armor, an engraved greatsword, and low-poly game topology by SpatialNeglect.',
                        'price_hint'    => 'Asset Reference: $150 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/the-queen-of-swords-875528a70ac845d0a67775c288ed3f5a',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/875528a70ac845d0a67775c288ed3f5a/thumbnails/49123c8443c84c6099027eebdf9a71d0/021fb3ae80df449c89f099fa108c1883.jpeg',
                        'embed_html'    => '<iframe title="The Queen of Swords" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/875528a70ac845d0a67775c288ed3f5a/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 2. Marcus Vance (Digital Painting & Environmental Art) ──────
            [
                'name'              => 'Marcus Vance',
                'email'             => 'marcus@artfolio.test',
                'slug'              => 'marcus_vance',
                'bio'               => 'Landscape painter and architectural concept illustrator crafting overgrown sanctuaries, perspective studies, and atmospheric light paintings.',
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Atmospheric Vistas & Sanctuaries',
                        'description' => 'Original digital paintings exploring natural lighting, architectural harmony, and solitude.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Secret Garden',
                        'description'   => 'Lush impressionistic digital environment illustration depicting sunbeams filtering through ancient stone archways and climbing flora by MilosLach.',
                        'price_hint'    => 'Fine Art Print: $75 USD',
                        'external_url'  => 'https://www.deviantart.com/miloslach/art/Secret-Garden-983483641',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/808ea18f-6867-4509-ad45-8e5065e1166e/dg9jgu1-d6247754-9105-42ad-92a8-3fac97265cd5.jpg/v1/fill/w_1096,h_729,q_70,strp/secret_garden_by_miloslach_dg9jgu1-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9MTE5OCIsInBhdGgiOiIvZi84MDhlYTE4Zi02ODY3LTQ1MDktYWQ0NS04ZTUwNjVlMTE2NmUvZGc5amd1MS1kNjI0Nzc1NC05MTA1LTQyYWQtOTJhOC0zZmFjOTcyNjVjZDUuanBnIiwid2lkdGgiOiI8PTE4MDAifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.j1FjB7aYHAC5A-DGpX9-PZNMGwPgUIUT_Z23ux8rg2g',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Landscape Drawing (Architecture Art)',
                        'description'   => 'Architectural perspective drawing investigating volumetric massing, classical structural framing, and line work by Andre Cadore.',
                        'price_hint'    => 'Architectural study drawing',
                        'external_url'  => 'https://www.deviantart.com/andrecadoreart/art/Landscape-Drawing-1399327015',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/77721fa5-9372-4660-84a1-37d45eb704bc/dnyz1y3-c90a1811-9a74-4b53-b0a5-f376ee178051.jpg/v1/fill/w_1024,h_768,q_70,strp/landscape_drawing__architecture_art__by_andrecadoreart_dnyz1y3-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9NzY4IiwicGF0aCI6Ii9mLzc3NzIxZmE1LTkzNzItNDY2MC04NGExLTM3ZDQ1ZWI3MDRiYy9kbnl6MXkzLWM5MGExODExLTlhNzQtNGI1My1iMGE1LWYzNzZlZTE3ODA1MS5qcGciLCJ3aWR0aCI6Ijw9MTAyNCJ9XV0sImF1ZCI6WyJ1cm46c2VydmljZTppbWFnZS5vcGVyYXRpb25zIl19.x8V4fE_f-vJq-eF2-a4-e9wXzC8',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Secret Ruins - Digital Painting Process & Commentary',
                        'description'   => 'Full digital painting demonstration and audio commentary exploring fantasy environment design, atmospheric lighting, and compositional depth by concept artist Grady Frederick.',
                        'price_hint'    => 'Educational Process Video',
                        'external_url'  => 'https://www.youtube.com/watch?v=tNJpz_7EcEE',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/tNJpz_7EcEE/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/tNJpz_7EcEE" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 3. Sofía Alarcón (Cinema & 3D Animation) ────────────────────
            [
                'name'              => 'Sofía Alarcón',
                'email'             => 'sofia@artfolio.test',
                'slug'              => 'sofia_alarcon',
                'bio'               => 'Cinematic filmmaker and animator curating celebrated open-source animation classics created with Blender.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.1,
                'collections'       => [
                    [
                        'name'        => 'Blender Open Movie Productions',
                        'description' => 'Internationally celebrated open-source short movies produced by the Blender Foundation.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Sintel - Open Movie by Blender Foundation',
                        'description'   => 'Acclaimed 3D animated fantasy short film exploring loss, companionship, and vengeance. Directed by Colin Levy for the Blender Open Movie initiative.',
                        'price_hint'    => 'Creative Commons Attribution (CC-BY)',
                        'external_url'  => 'https://www.youtube.com/watch?v=eRsGyueVLvQ',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/eRsGyueVLvQ/hqdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/eRsGyueVLvQ" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Big Buck Bunny - Open Animated Classic',
                        'description'   => 'Legendary open-source cartoon short directed by Sacha Goedegebure, introducing pioneering open furry character rigs and slapstick comedy.',
                        'price_hint'    => 'Public Open Archive / CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/YE7VzlLtp-4/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/YE7VzlLtp-4" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Tears of Steel - Open VFX Showcase',
                        'description'   => 'Dystopian science fiction short combining live-action anamorphic camera capture with full CGI robotics tracking in Amsterdam.',
                        'price_hint'    => 'Blender VFX Archive',
                        'external_url'  => 'https://www.youtube.com/watch?v=R6MlUcmOul8',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/R6MlUcmOul8/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/R6MlUcmOul8" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 4. Lucas Morel (Pop & Modern Music Production) ──────────────
            [
                'name'              => 'Lucas Morel',
                'email'             => 'lucas@artfolio.test',
                'slug'              => 'lucas_morel',
                'bio'               => 'Audio producer and multi-platinum recording enthusiast curating world-famous anthems and timeless pop hits.',
                'commission_status' => 'closed',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Modern Pop & Rock Anthems',
                        'description' => 'World-renowned chart-topping singles and legendary studio recordings.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Blinding Lights - The Weeknd',
                        'description'   => 'Record-breaking synthwave pop single powered by pulsing analog synthesizers, dynamic gated reverberation, and infectious 80s vocal melodies.',
                        'price_hint'    => 'Stream on Spotify',
                        'external_url'  => 'https://open.spotify.com/track/0VjIjW4GlUZAMYd2vXMi3b',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e028863bc11d2aa12b54f5aeb36',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/0VjIjW4GlUZAMYd2vXMi3b?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Mr. Brightside - The Killers',
                        'description'   => 'Definitive indie rock masterpiece built upon Dave Keuning’s iconic arpeggiated guitar riff and Brandon Flowers’ explosive vocal delivery.',
                        'price_hint'    => 'Island Records / UMG',
                        'external_url'  => 'https://open.spotify.com/track/3n3Ppam7vgaVa1iaRUc9Lp',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e029c284a6855f4945dc5a3cd73',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/3n3Ppam7vgaVa1iaRUc9Lp?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Shape of You - Ed Sheeran',
                        'description'   => 'Diamond-certified pop dancehall single crafted over an organic marimba groove and multi-track acoustic body percussion.',
                        'price_hint'    => 'Asylum Records UK',
                        'external_url'  => 'https://open.spotify.com/track/7qiZfU4dY1lWllzX7mPBI3',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e02ba5db46f4b838ef6027e6f96',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/7qiZfU4dY1lWllzX7mPBI3?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 5. Maya Chen (Game Design Systems & Production Docs) ────────
            [
                'name'              => 'Maya Chen',
                'email'             => 'maya@artfolio.test',
                'slug'              => 'maya_chen',
                'bio'               => 'Senior game designer and systems architect authoring comprehensive game design documentation and prototyping frameworks.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.05,
                'collections'       => [
                    [
                        'name'        => 'Game Design Bibles & Prototyping',
                        'description' => 'Production Game Design Documents (GDD), educational workshops, and systems documentation.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Game Design Document Template (Industry Standard)',
                        'description'   => 'Comprehensive production Game Design Document (GDD) template detailing core gameplay loops, interface breakdowns, and asset pipelines.',
                        'price_hint'    => 'Open Industry Resource',
                        'external_url'  => 'https://docs.google.com/document/d/1pO2i6OtVUBJrHm5F4xmaYsGf9pukKv2g5BbRPtalB90/edit?usp=sharing',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1pO2i6OtVUBJrHm5F4xmaYsGf9pukKv2g5BbRPtalB90&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1pO2i6OtVUBJrHm5F4xmaYsGf9pukKv2g5BbRPtalB90/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Quick Game Design Workshop (MIT edX Guide)',
                        'description'   => 'Educational workshop packet developed with MIT edX and the Institute of Play on rapid game ideation, rule drafting, and iterative playtesting.',
                        'price_hint'    => 'Academic Creative Commons',
                        'external_url'  => 'https://docs.google.com/document/d/1r01OYGV9e4Cuf-7UJoqAo2gbM-Y5q1l8zQu71FWex94/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1r01OYGV9e4Cuf-7UJoqAo2gbM-Y5q1l8zQu71FWex94&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1r01OYGV9e4Cuf-7UJoqAo2gbM-Y5q1l8zQu71FWex94/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Game Development & Math Reference Manual',
                        'description'   => 'Curated developer reference manual covering essential game math, vector transformations, state machines, and shader fundamentals.',
                        'price_hint'    => 'Open Developer Compendium',
                        'external_url'  => 'https://docs.google.com/document/d/1KziAOLFffTj_70Ib-xW68FaCGrWgMNPhMVsJkIx7B9s/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1KziAOLFffTj_70Ib-xW68FaCGrWgMNPhMVsJkIx7B9s&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1KziAOLFffTj_70Ib-xW68FaCGrWgMNPhMVsJkIx7B9s/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 6. Daniel Kim (Character Art & Pixel Illustration) ──────────
            [
                'name'              => 'Daniel Kim',
                'email'             => 'daniel@artfolio.test',
                'slug'              => 'daniel_kim',
                'bio'               => 'Character concept artist and pixel illustrator designing fantasy creatures, tabletop RPG adventurers, and cozy pixel landscapes.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Creatures, RPG Characters & Pixel Art',
                        'description' => 'Original character sheets, dark fantasy warrior concepts, and pixel art environments.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Spider Warrior',
                        'description'   => 'Dark fantasy arachnid warrior character illustration featuring chitinous armor plating, dual blades, and menacing crimson eyes by AlbertoV.',
                        'price_hint'    => 'Character Concept: $110 USD',
                        'external_url'  => 'https://www.deviantart.com/albertov/art/Spider-Warrior-1298180200',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/fe2c77df-b4fa-46aa-ba9f-f7adbc11e74c/dlgwi94-6cda350c-7f64-40d1-81cc-dba0c1bbf9a4.png?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiIvZi9mZTJjNzdkZi1iNGZhLTQ2YWEtYmE5Zi1mN2FkYmMxMWU3NGMvZGxnd2k5NC02Y2RhMzUwYy03ZjY0LTQwZDEtODFjYy1kYmEwYzFiYmY5YTQucG5nIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.HzIidC7ABUjP6NDrw5sXMr0s7EVGsuyO2dq_kDG4zIo',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Antay Version Final - Dnd Character',
                        'description'   => 'Tabletop RPG character concept featuring an elven rogue in leather tunic, daggers, and personalized adventuring gear by jampart2003.',
                        'price_hint'    => 'Custom RPG commission',
                        'external_url'  => 'https://www.deviantart.com/jampart2003/art/Antay-Version-Final-Dnd-1119117490',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/244113b2-1823-455b-b997-6a16b9cb8b9c/dgh0k5e-b8559092-be79-43c3-ae66-70eebcc25ee4.png/v1/fill/w_894,h_894,q_70,strp/antay_version_final___dnd_by_jampart2003_dgh0k5e-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9ODk0IiwicGF0aCI6Ii9mLzI0NDExM2IyLTE4MjMtNDU1Yi1iOTk3LTZhMTZiOWNiOGI5Yy9kZ2gwazVlLWI4NTU5MDkyLWJlNzktNDNjMy1hZTY2LTcwZWViY2MyNWVlNC5wbmciLCJ3aWR0aCI6Ijw9ODk0In1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.y2m8s9Vn3R8Z4B7cW3wF01777',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Lakeside Cottage on Foggy Morning',
                        'description'   => 'Atmospheric digital painting and pixel scenery capturing morning mist floating over a wooden cabin beside a reflective mountain lake by tanmu-u.',
                        'price_hint'    => 'Desktop Wallpaper & Print: $45 USD',
                        'external_url'  => 'https://www.deviantart.com/tanmu-u/art/Lakeside-cottage-on-foggy-morning-1269338093',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/432924f7-87eb-42d4-b778-ea48a738c641/djk8wh9-36a5fae5-0557-4560-aafe-21c609ec2543.png/v1/fill/w_1024,h_576,q_70,strp/lakeside_cottage_by_tanmu_u_djk8wh9-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9NTc2IiwicGF0aCI6Ii9mLzQzMjkyNGY3LTg3ZWItNDJkNC1iNzc4LWVhNDhhNzM4YzY0MS9kams4d2g5LTM2YTVmYWU1LTA1NTctNDU2MC1hYWZlLTIxYzYwOWVjMjU0My5wbmciLCJ3aWR0aCI6Ijw9MTAyNCJ9XV0sImF1ZCI6WyJ1cm46c2VydmljZTppbWFnZS5vcGVyYXRpb25zIl19.XjF132',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 7. Chloe Dubois (Whimsical 3D Dioramas & Low-Poly Scenes) ────
            [
                'name'              => 'Chloe Dubois',
                'email'             => 'chloe@artfolio.test',
                'slug'              => 'chloe_dubois',
                'bio'               => 'Environment and prop 3D artist crafting nautical dioramas, stylized campsites, and low-poly architectural skylines.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.15,
                'collections'       => [
                    [
                        'name'        => 'Whimsical 3D Dioramas & Scenes',
                        'description' => 'Interactive low-poly 3D models with hand-crafted textures and cozy lighting.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Ship in a Bottle (Interactive 3D Diorama)',
                        'description'   => 'Interactive 3D model of a detailed wooden galleon encased within a curved glass bottle, complete with realistic refraction by Loïc Norgeot.',
                        'price_hint'    => 'CC-BY Model Asset',
                        'external_url'  => 'https://sketchfab.com/3d-models/ship-in-a-bottle-9ddbc5b32da94bafbfdb56e1f6be9a38',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/9ddbc5b32da94bafbfdb56e1f6be9a38/thumbnails/21b228254d674aff8095dc2fc306b9d8/b292174f409b4212b4efeb45d3cdafa1.jpeg',
                        'embed_html'    => '<iframe title="Ship in a bottle" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/9ddbc5b32da94bafbfdb56e1f6be9a38/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Mercenary Hideout Spot (Modular Scene)',
                        'description'   => 'Atmospheric modular 3D environment featuring a secluded survival campsite with canvas tents, firepit, and wooden supply crates by SilkevdSmissen.',
                        'price_hint'    => 'Game Asset Pack: $45 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/mercenary-hideout-spot-5c8edf1cba5c465492e15a5f1d840ec2',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/5c8edf1cba5c465492e15a5f1d840ec2/thumbnails/55c26c09dc164085943a7abd82a3ea6c/e92e380067e1431fbd5edcef4d0c2774.jpeg',
                        'embed_html'    => '<iframe title="Mercenary Hideout Spot" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/5c8edf1cba5c465492e15a5f1d840ec2/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Low Poly Night City Building Skyline',
                        'description'   => 'Modular stylized urban buildings with emissive night windows, rooftop cooling units, and cozy city ambiance by 99.Miles.',
                        'price_hint'    => 'City Kit: $60 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/low-poly-night-city-building-skyline-b0035b8713b048bb8ddf311ee67c28c8',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/b0035b8713b048bb8ddf311ee67c28c8/thumbnails/a19e1d19ac684868b6d45ae8c1ee84c4/826cb92d2a574140bf8d6c36034d17d5.jpeg',
                        'embed_html'    => '<iframe title="Low Poly Night City" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/b0035b8713b048bb8ddf311ee67c28c8/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 8. Gabriel Santana (Motion Design & Character Animation) ────
            [
                'name'              => 'Gabriel Santana',
                'email'             => 'gabriel@artfolio.test',
                'slug'              => 'gabriel_santana',
                'bio'               => '3D character rigger and motion director showcasing modern procedural simulations from the Blender Studio.',
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Blender Studio Modern Shorts',
                        'description' => 'Visual milestones in expressive character animation, hair simulation, and comedy horror.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Spring - Blender Open Movie',
                        'description'   => 'Poetic fantasy animated short film following a young shepherdess and her loyal dog confronting ancient nature spirits to awaken spring.',
                        'price_hint'    => 'Creative Commons 4.0',
                        'external_url'  => 'https://www.youtube.com/watch?v=WhWc3b3KhnY',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/WhWc3b3KhnY/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/WhWc3b3KhnY" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Cosmos Laundromat - First Cycle',
                        'description'   => 'Absurdist sci-fi animation featuring Franck the sheep on a desolated island, demonstrating groundbreaking procedural fur dynamics.',
                        'price_hint'    => 'Blender Animation Archive',
                        'external_url'  => 'https://www.youtube.com/watch?v=Y-rmzh0PI3c',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/Y-rmzh0PI3c/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/Y-rmzh0PI3c" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Sprite Fright - Comedy Horror Short',
                        'description'   => '80s-inspired comedy horror short directed by Pixar veteran Matthew Luhn, following mischievous teenagers meeting territorial forest critters.',
                        'price_hint'    => 'Open Film Production',
                        'external_url'  => 'https://www.youtube.com/watch?v=_cMxraX_5RE',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://img.youtube.com/vi/_cMxraX_5RE/maxresdefault.jpg',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/_cMxraX_5RE" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 9. Aria Thorne (Dance, Funk & Legendary Anthems) ────────────
            [
                'name'              => 'Aria Thorne',
                'email'             => 'aria@artfolio.test',
                'slug'              => 'aria_thorne',
                'bio'               => 'Electronic music producer and DJ showcasing iconic funk-infused dance singles and rock masterworks.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.1,
                'collections'       => [
                    [
                        'name'        => 'Legendary Dance & Rock Classics',
                        'description' => 'Multi-platinum disco-funk grooves and historic stadium rock recordings.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Get Lucky - Daft Punk ft. Pharrell Williams',
                        'description'   => 'Grammy Record of the Year single by French electronic legends Daft Punk, driven by Nile Rodgers’ unmistakable funk guitar phrasing.',
                        'price_hint'    => 'Columbia Records',
                        'external_url'  => 'https://open.spotify.com/track/2Foc5Q5nqNiosCNqttzHof',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e0219a88dd5c7118e87d7b1619e',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/2Foc5Q5nqNiosCNqttzHof?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Bohemian Rhapsody - Queen',
                        'description'   => 'Historic 1975 operatic rock suite by Freddie Mercury and Queen, featuring multi-tracked vocal polyphony and explosive guitar solos.',
                        'price_hint'    => 'EMI Records Master',
                        'external_url'  => 'https://open.spotify.com/track/4u7EnebtmKWzUH433cf5Qv',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e02e319baafd16e84f0408af2a0',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/4u7EnebtmKWzUH433cf5Qv?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Never Gonna Give You Up - Rick Astley',
                        'description'   => 'The quintessential 1987 British dance-pop track produced by Stock Aitken Waterman, celebrated worldwide as an internet cultural landmark.',
                        'price_hint'    => 'RCA Records / PWL',
                        'external_url'  => 'https://open.spotify.com/track/4uLU6hMCjMI75M1A2tKUQC',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://image-cdn-ak.spotifycdn.com/image/ab67616d00001e02255e131abc1410833be95673',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/4uLU6hMCjMI75M1A2tKUQC?utm_source=generator" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 10. Tariq Al-Mansoor (Cyber Armory & Community Art) ─────────
            [
                'name'              => 'Tariq Al-Mansoor',
                'email'             => 'tariq@artfolio.test',
                'slug'              => 'tariq_almansoor',
                'bio'               => 'Hard-surface 3D modeler and digital artist showcasing armored sci-fi warriors, power armor, and community digital pieces.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Cyber Armory & Community Art',
                        'description' => 'Next-gen exoskeleton models, power armor rigs, and celebratory digital art pieces.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Cyber Samurai (Game-Ready Character)',
                        'description'   => 'High-poly 3D character mesh fusing traditional feudal Japanese kabuto armor with futuristic cybernetic prosthetics by KhoaMinh.',
                        'price_hint'    => 'Game Ready Rig: $180 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/cyber-samurai-26ccafaddb2745ceb56ae5cfc65bfed5',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/26ccafaddb2745ceb56ae5cfc65bfed5/thumbnails/1e3cb3c710a04f96a9fdad8394dd492e/4ff8482d739e49a7aa7013838d248439.jpeg',
                        'embed_html'    => '<iframe title="Cyber Samurai" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/26ccafaddb2745ceb56ae5cfc65bfed5/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Spartan Armour MKV - Halo Reach',
                        'description'   => 'PBR real-time 3D recreation of the legendary Mark V MJOLNIR powered battle armor worn by supersoldiers by McCarthy3D.',
                        'price_hint'    => 'Tribute Asset',
                        'external_url'  => 'https://sketchfab.com/3d-models/spartan-armour-mkv-halo-reach-57070b2fd9ff472c8988e76d8c5cbe66',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://media.sketchfab.com/models/57070b2fd9ff472c8988e76d8c5cbe66/thumbnails/a57788fd1bc84ecba2c28818d922cfcf/c8b16ee642264d47ab3ca393f882183e.jpeg',
                        'embed_html'    => '<iframe title="Spartan Armour MKV" frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="autoplay; fullscreen; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="https://sketchfab.com/models/57070b2fd9ff472c8988e76d8c5cbe66/embed" class="w-full h-full aspect-video rounded-xl"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Fella Celebrates 100k',
                        'description'   => 'Official commemorative digital illustration celebrating community creative milestones on DeviantArt with mascot Fella by Team.',
                        'price_hint'    => 'DeviantArt Official Community Art',
                        'external_url'  => 'https://www.deviantart.com/team/art/Fella-Celebrates-100k-971957229',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/eae614f8-f83b-4837-935b-ab22f4298eb2/dg2oezx-09bb2529-2f57-4fd6-ae86-dbf36c7a7dde.jpg/v1/fill/w_774,h_1032,q_70,strp/fella_celebrates_100k_by_team_dg2oezx-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9MTcwNyIsInBhdGgiOiIvZi9lYWU2MTRmOC1mODNiLTQ4MzctOTM1Yi1hYjIyZjQyOThlYjIvZGcyb2V6eC0wOWJiMjUyOS0yZjU3LTRmZDYtYWU4Ni1kYmYzNmM3YTdkZGUuanBnIiwid2lkdGgiOiI8PTEyODAifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.qyDDa3d4UfNO4PLTM-sS-QVlg8SxOMoNem3w0i0pnq0',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 11. Sarah Jenkins (Educational Game Curriculum & GDDs) ──────
            [
                'name'              => 'Sarah Jenkins',
                'email'             => 'sarah@artfolio.test',
                'slug'              => 'sarah_jenkins',
                'bio'               => 'Educational game curriculum author and narrative designer designing classroom playtesting packets and pitch frameworks.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Game Prototyping & Teaching Packets',
                        'description' => 'Curriculum packets, rapid pitch frameworks, and comprehensive game development documents.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Missouri Scholars Academy Game Design Packet',
                        'description'   => 'Complete educational curriculum and workshop packet developed for the Missouri Scholars Academy teaching iterative game prototyping and tabletop rules.',
                        'price_hint'    => 'Educational Teaching Resource',
                        'external_url'  => 'https://docs.google.com/document/d/1BxBLzSecZ45u1H3uLeoAD1Ig17M2F9DXUZchzsCgkII/edit?usp=sharing',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1BxBLzSecZ45u1H3uLeoAD1Ig17M2F9DXUZchzsCgkII&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1BxBLzSecZ45u1H3uLeoAD1Ig17M2F9DXUZchzsCgkII/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'One Page Game Design Document Framework',
                        'description'   => 'Lean single-page GDD template optimized for game jams, executive summaries, design pillars, and rapid production pitches.',
                        'price_hint'    => 'Open Indie Template',
                        'external_url'  => 'https://docs.google.com/document/d/1npEvqcMZSp0IX2hWw6Qq0WqJVfmVqS_YOGFWnnwfh-A/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1npEvqcMZSp0IX2hWw6Qq0WqJVfmVqS_YOGFWnnwfh-A&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1npEvqcMZSp0IX2hWw6Qq0WqJVfmVqS_YOGFWnnwfh-A/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Game Design Document Comprehensive Template',
                        'description'   => 'Production framework for multidisciplinary teams covering mechanical balance tables, sound effects logs, and sprint task milestones.',
                        'price_hint'    => 'Public GDD Compendium',
                        'external_url'  => 'https://docs.google.com/document/d/1axeeBWp683LPU8gCBQQqmquHMYHuG3uhNTN0LjSJBKk/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://drive.google.com/thumbnail?id=1axeeBWp683LPU8gCBQQqmquHMYHuG3uhNTN0LjSJBKk&sz=w800',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1axeeBWp683LPU8gCBQQqmquHMYHuG3uhNTN0LjSJBKk/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                ],
            ],

            // ── 12. Kenji Takahashi (Traditional Art & Marine Paintings) ────
            [
                'name'              => 'Kenji Takahashi',
                'email'             => 'kenji@artfolio.test',
                'slug'              => 'kenji_takahashi',
                'bio'               => 'Visual artist and traditional painter creating bioluminescent marine paintings, wildlife studies, and peaceful impressionistic encounters.',
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.05,
                'collections'       => [
                    [
                        'name'        => 'Marine Life & Nature Impressions',
                        'description' => 'Original digital and pastel artwork exploring underwater transparency, birds, and natural beauty.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Jellyfish (Bioluminescent Marine Painting)',
                        'description'   => 'Luminous digital painting capturing the graceful, translucent bell of a deep-sea jellyfish undulating through dark ocean waters by funarium.',
                        'price_hint'    => 'Fine Art Print: $65 USD',
                        'external_url'  => 'https://www.deviantart.com/funarium/art/Jellyfish-1350570293',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/1659d956-fa29-4da3-8b8f-5f4c0c2c184e/dmc3eph-09aeb255-d195-4ad6-a9c1-657b4aafdc7b.png/v1/fill/w_1063,h_752,q_70,strp/jellyfish_by_funarium_dmc3eph-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9ODQyIiwicGF0aCI6Ii9mLzE2NTlkOTU2LWZhMjktNGRhMy04YjhmLTVmNGMwYzJjMTg0ZS9kbWMzZXBoLTA5YWViMjU1LWQxOTUtNGFkNi1hOWMxLTY1N2I0YWFmZGM3Yi5wbmciLCJ3aWR0aCI6Ijw9MTE5MSJ9XV0sImF1ZCI6WyJ1cm46c2VydmljZTppbWFnZS5vcGVyYXRpb25zIl19.XAlCk72dURPy78jS6Dibe5zzt0oPoPU3aJ9_nPjevdU',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Dove (Expressive Textured Fine Art)',
                        'description'   => 'Textured fine art study portraying a white dove in flight against an earthy, weathered background by Abbadier.',
                        'price_hint'    => 'Original Oil Pastel: $140 USD',
                        'external_url'  => 'https://www.deviantart.com/abbadier/art/Dove-1398478932',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/d17f1b74-dea2-4fcf-b673-dfc545eb11dc/do4hgg0-61d0f5bf-7013-4414-b52e-fb111be1ae3d.jpg/v1/fill/w_800,h_800,q_70,strp/dove_by_abbadier_do4hgg0-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9ODAwIiwicGF0aCI6Ii9mL2QxN2YxYjc0LWRlYTItNGZjZi1iNjczLWRmYzU0NWViMTFkYy9kbzRoZ2cwLTYxZDBmNWJmLTcwMTMtNDQxNC1iNTJlLWZiMTExYmUxYWUzZC5qcGciLCJ3aWR0aCI6Ijw9ODAwIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.vB0r73',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                    [
                        'title'         => 'Une belle rencontre (Impressionist Oil Painting)',
                        'description'   => 'Warm impressionistic painting depicting human encounters in a sunlit public square by TABLEAUXTOMPAT.',
                        'price_hint'    => 'Gallery Exhibition piece',
                        'external_url'  => 'https://www.deviantart.com/tableauxtompat/art/Une-belle-rencontre-1407002408',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/e6843bc7-a339-445a-8b83-f3684a0d8e27/doaqn1c-32f281e2-b131-412e-9d21-f09b55570889.jpg/v1/fill/w_800,h_800,q_70,strp/une_belle_rencontre_by_tableauxtompat_doaqn1c-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9ODAwIiwicGF0aCI6Ii9mL2U2ODQzYmM3LWEzMzktNDQ1YS04YjgzLWYzNjg0YTBkOGUyNy9kb2FxbjFjLTMyZjI4MWUyLWIxMzEtNDEyZS05ZDIxLWYwOWI1NTU3MDg4OS5qcGciLCJ3aWR0aCI6Ijw9ODAwIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.y2m8s9',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                ],
            ],
        ];

        // ─────────────────────────────────────────────────────────────────────
        // 2. CLEANUP LEGACY DEMO CONTENT (Removes any old fake/non-existent entries)
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
        // 3. PERSIST REAL DEMO CREATORS, COLLECTIONS & ARTWORKS
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

                // Attach to creator's collection
                if (!empty($createdCollections)) {
                    $artwork->collections()->syncWithoutDetaching([$createdCollections[0]->id]);
                }

                $createdArtworks[] = $artwork;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. CROSS-BOOKMARKS (Favoriting other creators' real works)
        // ─────────────────────────────────────────────────────────────────────

        if (count($createdUsers) >= 8 && count($createdArtworks) >= 16) {
            // Elena (3D) bookmarks Marcus's Secret Garden and Sofia's Sintel
            Bookmark::updateOrCreate(['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[3]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[6]->id]);

            // Marcus (Image) bookmarks Elena's Rosetta Stone and Daniel's Spider Warrior
            Bookmark::updateOrCreate(['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[0]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[15]->id]);

            // Sofia (Video) bookmarks Lucas's Blinding Lights and Gabriel's Spring
            Bookmark::updateOrCreate(['user_id' => $createdUsers[2]->id, 'artwork_id' => $createdArtworks[9]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[2]->id, 'artwork_id' => $createdArtworks[21]->id]);

            // Lucas (Audio) bookmarks Maya's GDD and Aria's Get Lucky
            Bookmark::updateOrCreate(['user_id' => $createdUsers[3]->id, 'artwork_id' => $createdArtworks[12]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[3]->id, 'artwork_id' => $createdArtworks[24]->id]);

            // Maya (Doc) bookmarks Tariq's Cyber Samurai and Sarah's Design Packet
            Bookmark::updateOrCreate(['user_id' => $createdUsers[4]->id, 'artwork_id' => $createdArtworks[27]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[4]->id, 'artwork_id' => $createdArtworks[30]->id]);

            // Daniel (Pixel/Creature) bookmarks Kenji's Jellyfish and Chloe's Ship in a Bottle
            Bookmark::updateOrCreate(['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[33]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[5]->id, 'artwork_id' => $createdArtworks[18]->id]);

            // Chloe (3D) bookmarks Elena's Rosetta Stone and Kenji's Dove
            Bookmark::updateOrCreate(['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[0]->id]);
            Bookmark::updateOrCreate(['user_id' => $createdUsers[6]->id, 'artwork_id' => $createdArtworks[34]->id]);

            // Gabriel (Motion) bookmarks Sofia's Sintel and Marcus's Ghost
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
                    'artwork_id' => $createdArtworks[1]->id, // Second artwork
                ],
                [
                    'reason'     => 'Broken or inaccessible external link',
                    'details'    => 'The external 3D viewer experienced high latency on low-bandwidth connections. Verified and active.',
                    'status'     => 'pending',
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artwork;
use App\Models\Collection;
use App\Models\Bookmark;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        // 1. ARTISTAS DE DEMOSTRACIÓN
        // ─────────────────────────────────────────────────────────────────────

        $artistsData = [
            [
                'name'              => 'Elena Rostova',
                'email'             => 'elena@artfolio.test',
                'slug'              => 'elena_rostova',
                'bio'               => "Escultora digital y artista 3D especializada en reinterpretaciones anatómicas, estatuaria helénica y props de fantasía épica para videojuegos.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb',
                'crop_x'            => 0,
                'crop_y'            => -5,
                'crop_zoom'         => 1.2,
                'collections'       => [
                    [
                        'name'        => 'Esculturas y Arqueología Digital',
                        'description' => 'Modelos tridimensionales inspirados en el arte clásico y la mitología.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Escultura Helénica de Mármol',
                        'description'   => 'Estudio de drapeado y anatomía clásica romana. Recreación poligonal optimizada para renderizado en tiempo real.',
                        'price_hint'    => 'Orientativo: $120 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/helenic-sculpture-asset',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Relieve Mitológico en Bronce',
                        'description'   => 'Boceto tridimensional de un friso ornamental con acabados de pátina antigua.',
                        'price_hint'    => 'Orientativo: $95 USD',
                        'external_url'  => 'https://sketchfab.com/3d-models/bronze-relief-classical',
                        'provider'      => 'sketchfab',
                        'category'      => '3d',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1563089145-599997674d42',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],
            [
                'name'              => 'Marcus Vance',
                'email'             => 'marcus@artfolio.test',
                'slug'              => 'marcus_vance',
                'bio'               => "Ilustrador conceptual y matte painter. Explorando la intersección entre la fantasía medieval y el retro-futurismo en formato digital.",
                'commission_status' => 'consult',
                'avatar_url'        => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Paisajes Épicos y Concept Art',
                        'description' => 'Escenarios de fantasía y composiciones de gran escala.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'El Templo de las Nebulosas',
                        'description'   => 'Ilustración digital sobre un santuario ancestral en las cumbres montañosas. Creado con técnicas mixtas de pintura digital.',
                        'price_hint'    => 'Orientativo: $150 USD',
                        'external_url'  => 'https://www.deviantart.com/marcusvance/art/Temple-of-Nebulas-982101',
                        'provider'      => 'deviantart',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23',
                        'embed_html'    => null,
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Caballero del Sol Poniente',
                        'description'   => 'Diseño de personaje y armadura ornamental dorada inspirada en la iconografía renacentista.',
                        'price_hint'    => 'Orientativo: $110 USD',
                        'external_url'  => 'https://www.pinterest.com/pin/109212345678912345/',
                        'provider'      => 'pinterest',
                        'category'      => 'image',
                        'render_type'   => 'image',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477',
                        'embed_html'    => null,
                        'is_featured'   => false,
                    ],
                ],
            ],
            [
                'name'              => 'Sofía Alarcón',
                'email'             => 'sofia@artfolio.test',
                'slug'              => 'sofia_alarcon',
                'bio'               => "Directora de animación independiente y animadora 2D. Amante del motion design expresivo y cortometrajes de código abierto.",
                'commission_status' => 'closed',
                'avatar_url'        => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330',
                'crop_x'            => -5,
                'crop_y'            => -8,
                'crop_zoom'         => 1.3,
                'collections'       => [
                    [
                        'name'        => 'Cortometrajes Abiertos',
                        'description' => 'Proyectos cinematográficos y animaciones bajo licencias abiertas.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Sintel - Cortometraje Abierto',
                        'description'   => 'Aclamado cortometraje de animación 3D de fantasía épica producido por la Fundación Blender. Banda sonora original y texturas de alta definición.',
                        'price_hint'    => 'Licencia Creative Commons',
                        'external_url'  => 'https://www.youtube.com/watch?v=eRsGyueVLvQ',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/eRsGyueVLvQ" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Big Buck Bunny - Clásico Animado',
                        'description'   => 'Comedia animada sobre los bosques y la naturaleza. Hito en el software libre y la animación por computadora.',
                        'price_hint'    => 'Dominio Público / CC-BY',
                        'external_url'  => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
                        'provider'      => 'youtube',
                        'category'      => 'video',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba',
                        'embed_html'    => '<iframe src="https://www.youtube.com/embed/YE7VzlLtp-4" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full aspect-video rounded-lg"></iframe>',
                        'is_featured'   => false,
                    ],
                ],
            ],
            [
                'name'              => 'Lucas Morel',
                'email'             => 'lucas@artfolio.test',
                'slug'              => 'lucas_morel',
                'bio'               => "Compositor sonoro y diseñador de audio interactivo. Creo atmósferas ambientales, texturas orquestales y paisajes acústicos.",
                'commission_status' => 'open',
                'avatar_url'        => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e',
                'crop_x'            => 0,
                'crop_y'            => 0,
                'crop_zoom'         => 1.0,
                'collections'       => [
                    [
                        'name'        => 'Composiciones & Diseño de Audio',
                        'description' => 'Muestras de audio orquestal y documentación técnica de diseño sonoro.',
                    ],
                ],
                'artworks'          => [
                    [
                        'title'         => 'Sinfonía No. 5 en Do Menor (Muestra)',
                        'description'   => 'Interpretación orquestal de una obra maestra clásica inmortal. Pieza de dominio público para ambientación épica.',
                        'price_hint'    => 'Dominio Público',
                        'external_url'  => 'https://open.spotify.com/track/1Pt5p3vLzJc5x9P3a4L6',
                        'provider'      => 'spotify',
                        'category'      => 'audio',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
                        'embed_html'    => '<iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/1Pt5p3vLzJc5x9P3a4L6" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-presentation allow-popups" loading="lazy"></iframe>',
                        'is_featured'   => true,
                    ],
                    [
                        'title'         => 'Guía de Composición y Dirección de Sonido',
                        'description'   => 'Documento de especificaciones y notas creativas sobre la integración de sonido ambiental en videojuegos.',
                        'price_hint'    => 'Documento Abierto',
                        'external_url'  => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit',
                        'provider'      => 'google_docs',
                        'category'      => 'doc',
                        'render_type'   => 'iframe',
                        'thumbnail_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a',
                        'embed_html'    => '<iframe src="https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/preview" sandbox="allow-scripts allow-same-origin allow-presentation" frameborder="0" class="w-full h-full min-h-[550px] rounded-lg"></iframe>',
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
        if (count($createdUsers) >= 2 && count($createdArtworks) >= 4) {
            // Elena guarda la obra de Marcus y la de Sofía
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[2]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[0]->id, 'artwork_id' => $createdArtworks[4]->id]
            );

            // Marcus guarda la escultura de Elena y el audio de Lucas
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[0]->id]
            );
            Bookmark::updateOrCreate(
                ['user_id' => $createdUsers[1]->id, 'artwork_id' => $createdArtworks[6]->id]
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
                    'reason'     => 'Enlace caído o inaccesible',
                    'details'    => 'El modelo 3D tarda demasiado en responder desde el servidor remoto. Sugiero verificar la disponibilidad.',
                    'status'     => 'pending',
                ]
            );
        }
    }
}

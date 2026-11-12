<?php

namespace Database\Seeders;

use App\Models\CuratedTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    /**
     * Seed curated disciplines and software tools for the art platform.
     */
    public function run(): void
    {
        $disciplines = [
            'Concept Art',
            'Digital Artist',
            'Virtual Artist',
            'Character Design',
            '3D Modeling',
            'Digital Painting',
            'Illustration',
            'Environment Art',
            'Animation',
            'Sculpting',
            'Digital Sculpting',
            'Storyboarding',
            'Texturing',
            'UI / UX Art',
            'VFX',
            'Matte Painting',
            'Lighting & LookDev',
            'Game Art',
            'Motion Graphics',
            'Pixel Art',
            'Sound Design',
        ];

        $software = [
            'Blender',
            'Photoshop',
            'ZBrush',
            'Unreal Engine',
            'Maya',
            'Substance 3D Painter',
            'Procreate',
            'Cinema 4D',
            'After Effects',
            'Houdini',
            'Figma',
            'Illustrator',
            'Unity',
            'Marvelous Designer',
            'Clip Studio Paint',
            'DaVinci Resolve',
            'Godot',
            '3ds Max',
        ];

        foreach ($disciplines as $order => $name) {
            CuratedTag::firstOrCreate(
                ['type' => 'discipline', 'name' => $name],
                [
                    'slug'         => Str::slug($name),
                    'is_suggested' => true,
                    'sort_order'   => $order + 1,
                ]
            );
        }

        foreach ($software as $order => $name) {
            CuratedTag::firstOrCreate(
                ['type' => 'software', 'name' => $name],
                [
                    'slug'         => Str::slug($name),
                    'is_suggested' => true,
                    'sort_order'   => $order + 1,
                ]
            );
        }
    }
}


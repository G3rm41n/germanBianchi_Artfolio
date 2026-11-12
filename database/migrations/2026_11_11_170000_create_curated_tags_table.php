<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curated_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 20)->index(); // 'discipline' or 'software'
            $table->string('slug', 120)->index();
            $table->boolean('is_suggested')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['type', 'slug']);
        });

        // Seed initial default tags
        $defaultDisciplines = [
            '3D Modeling', 'Concept Art', 'Illustration', 'Character Design',
            'Environment Art', 'Animation', 'Matte Painting', 'Storyboarding',
            'UI / UX Art', 'Digital Painting', 'Texturing', 'Lighting', 'Sculpting'
        ];

        $defaultSoftware = [
            'Blender', 'Photoshop', 'ZBrush', 'Unreal Engine', 'Maya',
            'Substance 3D Painter', 'Procreate', 'Cinema 4D', 'After Effects',
            'Houdini', 'Figma', 'Illustrator', 'Unity', 'Marvelous Designer'
        ];

        $now = now();
        $inserts = [];

        foreach ($defaultDisciplines as $idx => $name) {
            $inserts[] = [
                'name' => $name,
                'type' => 'discipline',
                'slug' => Str::slug($name),
                'is_suggested' => true,
                'sort_order' => $idx,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($defaultSoftware as $idx => $name) {
            $inserts[] = [
                'name' => $name,
                'type' => 'software',
                'slug' => Str::slug($name),
                'is_suggested' => true,
                'sort_order' => $idx,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('curated_tags')->insert($inserts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curated_tags');
    }
};


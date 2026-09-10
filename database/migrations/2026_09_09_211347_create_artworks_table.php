<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('price_hint', 100)->nullable();       // precio orientativo
            $table->text('external_url');                        // URL del recurso externo
            $table->string('provider', 50);                      // ej: 'youtube', 'sketchfab'
            $table->enum('category', ['3d', 'video', 'audio', 'image', 'document']);
            $table->enum('render_type', ['iframe', 'image', 'audio', 'document']);
            $table->text('thumbnail_url')->nullable();
            $table->text('embed_html')->nullable();
            $table->enum('status', ['published', 'hidden'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Índice compuesto para filtros de galería (RNF-03)
            $table->index(['category', 'status', 'deleted_at', 'created_at'],
                          'artworks_gallery_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};

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
        Schema::create('artwork_collection', function (Blueprint $table) {
            $table->foreignId('artwork_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('collection_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Evita duplicados en la tabla pivot
            $table->unique(['artwork_id', 'collection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_collection');
    }
};

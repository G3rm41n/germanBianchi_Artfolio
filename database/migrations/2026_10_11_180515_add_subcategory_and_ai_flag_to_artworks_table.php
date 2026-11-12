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
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('subcategory', 100)->nullable()->after('category');
            $table->boolean('is_ai_generated')->default(false)->after('subcategory');
            $table->index(['is_ai_generated', 'status', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropIndex(['is_ai_generated', 'status', 'deleted_at']);
            $table->dropColumn(['subcategory', 'is_ai_generated']);
        });
    }
};

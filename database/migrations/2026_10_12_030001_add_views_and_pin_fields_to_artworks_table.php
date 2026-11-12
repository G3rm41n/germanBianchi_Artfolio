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
            $table->unsignedBigInteger('views_count')->default(0)->after('is_ai_generated');
            $table->boolean('is_pinned')->default(false)->after('views_count');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'is_pinned', 'pinned_at']);
        });
    }
};


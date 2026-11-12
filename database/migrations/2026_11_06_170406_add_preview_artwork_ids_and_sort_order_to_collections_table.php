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
        Schema::table('collections', function (Blueprint $table) {
            $table->json('preview_artwork_ids')->nullable()->after('visibility');
            $table->unsignedInteger('sort_order')->default(0)->after('preview_artwork_ids');
            $table->timestamp('last_artwork_added_at')->nullable()->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['preview_artwork_ids', 'sort_order', 'last_artwork_added_at']);
        });
    }
};

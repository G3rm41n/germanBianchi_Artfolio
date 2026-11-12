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
            $table->unsignedBigInteger('likes_count')->default(0)->after('views_count');
            $table->json('software_used')->nullable()->after('subcategory');
            $table->string('license', 40)->default('all_rights_reserved')->after('status');
        });

        Schema::create('artwork_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['artwork_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_likes');

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'software_used', 'license']);
        });
    }
};

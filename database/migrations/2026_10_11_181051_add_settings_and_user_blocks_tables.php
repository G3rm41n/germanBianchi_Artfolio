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
        Schema::table('users', function (Blueprint $table) {
            $table->string('banner_url', 2048)->nullable()->after('avatar_url');
            $table->boolean('show_mature_content')->default(false)->after('contact_email');
            $table->boolean('hide_ai_content')->default(false)->after('show_mature_content');
            $table->boolean('notify_new_artworks')->default(true)->after('hide_ai_content');
            $table->boolean('notify_messages')->default(true)->after('notify_new_artworks');
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'blocked_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_blocks');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'banner_url',
                'show_mature_content',
                'hide_ai_content',
                'notify_new_artworks',
                'notify_messages',
            ]);
        });
    }
};

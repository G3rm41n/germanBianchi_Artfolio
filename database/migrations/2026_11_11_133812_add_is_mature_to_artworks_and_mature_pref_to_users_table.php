<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_mature to artworks
        Schema::table('artworks', function (Blueprint $table) {
            $table->boolean('is_mature')->default(false)->after('is_ai_generated');
            $table->index(['is_mature', 'status', 'deleted_at'], 'artworks_mature_status_index');
        });

        // 2. Add mature_filter_preference to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('mature_filter_preference', 20)->default('blur')->after('contact_email');
        });

        // 3. Migrate existing boolean show_mature_content values if column exists
        if (Schema::hasColumn('users', 'show_mature_content')) {
            DB::table('users')->where('show_mature_content', 1)->update(['mature_filter_preference' => 'show']);
            DB::table('users')->where('show_mature_content', 0)->update(['mature_filter_preference' => 'blur']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropIndex('artworks_mature_status_index');
            $table->dropColumn('is_mature');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mature_filter_preference');
        });
    }
};

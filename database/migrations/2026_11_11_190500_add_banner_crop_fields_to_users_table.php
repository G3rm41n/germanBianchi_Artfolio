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
            $table->float('banner_crop_x')->default(0)->after('banner_url');
            $table->float('banner_crop_y')->default(0)->after('banner_crop_x');
            $table->float('banner_crop_zoom')->default(1)->after('banner_crop_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['banner_crop_x', 'banner_crop_y', 'banner_crop_zoom']);
        });
    }
};

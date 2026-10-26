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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE artworks MODIFY COLUMN category ENUM('3d', 'video', 'audio', 'image', 'document', 'doc') NOT NULL");
        \Illuminate\Support\Facades\DB::statement("UPDATE artworks SET category = 'doc' WHERE category = 'document'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("UPDATE artworks SET category = 'document' WHERE category = 'doc'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE artworks MODIFY COLUMN category ENUM('3d', 'video', 'audio', 'image', 'document') NOT NULL");
    }
};

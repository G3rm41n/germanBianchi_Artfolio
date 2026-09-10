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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // quien reporta
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 100);           // motivo del reporte (ej: 'Enlace caído')
            $table->text('details')->nullable();     // descripción adicional
            $table->text('admin_note')->nullable();  // nota interna del administrador
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->timestamps();

            // Previene reportes duplicados pendientes del mismo usuario (RF-09)
            $table->unique(['user_id', 'artwork_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

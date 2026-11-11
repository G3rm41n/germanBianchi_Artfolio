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
            // Step 1: Personal Profile fields
            $table->string('headline', 120)->nullable()->after('name');
            $table->string('city', 100)->nullable()->after('headline');
            $table->string('country', 100)->nullable()->after('city');

            // Step 2: Professional Profile fields
            $table->json('skills')->nullable()->after('bio');
            $table->json('software')->nullable()->after('skills');
            $table->json('hiring_types')->nullable()->after('software');
            $table->boolean('public_email_enabled')->default(false)->after('hiring_types');

            // Onboarding Progress Tracking
            $table->unsignedTinyInteger('onboarding_step')->default(1)->after('status');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'headline',
                'city',
                'country',
                'skills',
                'software',
                'hiring_types',
                'public_email_enabled',
                'onboarding_step',
                'onboarding_completed_at',
            ]);
        });
    }
};

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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('facility_type', 20);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email_institution', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->json('operating_hours')->nullable();
            $table->text('description')->nullable();
            $table->json('facilities')->nullable(); // array of facility features/services
            $table->timestamps();

            $table->unique('tenant_id');
            $table->index('facility_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};

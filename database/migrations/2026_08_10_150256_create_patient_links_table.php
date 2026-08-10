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
        Schema::create('patient_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('facility_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('link_code', 20)->unique();
            $table->string('status', 20)->default('pending'); // pending, active, revoked
            $table->json('permissions')->nullable(); // {view_health: true, edit_growth: false, ...}
            $table->timestamp('linked_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['child_id', 'parent_user_id']);
            $table->index(['parent_user_id', 'status']);
            $table->index(['facility_tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_links');
    }
};

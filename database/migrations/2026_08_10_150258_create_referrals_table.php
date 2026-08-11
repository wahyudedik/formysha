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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('from_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('to_tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('referring_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->text('clinical_summary')->nullable();
            $table->string('status', 20)->default('pending'); // pending, accepted, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['child_id', 'status']);
            $table->index(['from_tenant_id', 'status']);
            $table->index(['to_tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};

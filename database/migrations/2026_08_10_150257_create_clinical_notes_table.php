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
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // consultation, examination, treatment, follow-up
            $table->string('title', 255);
            $table->text('content');
            $table->json('vitals')->nullable(); // {temperature, heart_rate, blood_pressure, weight, height}
            $table->text('diagnosis')->nullable();
            $table->json('medications')->nullable(); // [{name, dosage, frequency, duration}]
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['child_id', 'tenant_id']);
            $table->index(['tenant_id', 'type']);
            $table->index(['staff_user_id', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};

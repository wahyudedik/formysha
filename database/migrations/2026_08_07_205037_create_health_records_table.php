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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['immunization', 'illness', 'medication', 'allergy', 'checkup', 'other']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('doctor')->nullable();
            $table->string('hospital')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_date')->nullable();
            $table->timestamps();

            $table->index(['child_id', 'type']);
            $table->index(['child_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};

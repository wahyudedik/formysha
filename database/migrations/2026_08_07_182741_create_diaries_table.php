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
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->enum('mood', ['happy', 'excited', 'calm', 'sad', 'surprised', 'loved'])->nullable();
            $table->date('diary_date');
            $table->string('weather')->nullable();
            $table->boolean('is_private')->default(true);
            $table->timestamps();

            $table->index('child_id');
            $table->index('user_id');
            $table->index('diary_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};

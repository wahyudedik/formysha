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
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price_monthly')->default(0);
            $table->integer('price_yearly')->nullable();
            $table->integer('max_children')->default(1);
            $table->integer('max_photos')->default(50);
            $table->integer('max_videos')->default(10);
            $table->integer('max_storage_mb')->default(500);
            $table->integer('max_family_members')->nullable()->default(5);
            $table->integer('max_export_per_day')->default(3);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

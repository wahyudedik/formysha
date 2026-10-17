<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom is_free dan audience ke tabel plans.
     * - is_free: menandai plan gratis yang diaktifkan langsung tanpa pembayaran.
     * - audience: menandai target plan — 'family' (B2C) atau 'facility' (B2B).
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('is_active');
            $table->string('audience', 20)->default('family')->after('is_free');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_free', 'audience']);
        });
    }
};

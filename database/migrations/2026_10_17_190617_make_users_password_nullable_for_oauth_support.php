<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * Kolom password dibuat nullable untuk mendukung akun OAuth/Socialite
     * yang tidak memiliki kata sandi lokal. Tanpa ini, pengguna OAuth tidak
     * dapat login sama sekali (constraint NOT NULL pada kolom password).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke NOT NULL — pastikan tidak ada baris password null
            $table->string('password')->nullable(false)->change();
        });
    }
};

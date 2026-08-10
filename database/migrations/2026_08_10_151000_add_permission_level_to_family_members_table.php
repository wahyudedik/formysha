<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->string('permission_level')->default('view')->after('is_primary');
        });

        // Set default permission based on relationship
        DB::table('family_members')
            ->whereIn('relationship', ['father', 'mother', 'guardian'])
            ->update(['permission_level' => 'edit']);

        DB::table('family_members')
            ->whereNotIn('relationship', ['father', 'mother', 'guardian'])
            ->update(['permission_level' => 'view']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn('permission_level');
        });
    }
};

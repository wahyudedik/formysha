<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that need a tenant_id column.
     */
    private array $tables = [
        'children',
        'timelines',
        'media',
        'albums',
        'diaries',
        'documents',
        'events',
        'growths',
        'health_records',
        'family_members',
        'notifications',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add tenant_id to all existing child-related tables
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->uuid('tenant_id')->nullable()->after('id');
                $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
                $table->index('tenant_id');
            });
        }

        // Add tenant_id and update role column for users table
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
            $table->index('tenant_id');
        });

        // Convert role enum to VARCHAR(20) for SaaS flexibility
        // In PostgreSQL, we alter the column type directly
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('parent')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
            // Note: We cannot easily revert VARCHAR back to enum in PostgreSQL
            // without knowing the original enum type name
        });
    }
};

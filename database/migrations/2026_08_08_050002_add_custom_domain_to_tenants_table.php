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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique()->after('domain');
            $table->timestamp('domain_verified_at')->nullable()->after('custom_domain');
            $table->boolean('domain_dns_verified')->default(false)->after('domain_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'domain_verified_at', 'domain_dns_verified']);
        });
    }
};

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
            $table->string('type', 20)->default('family')->after('name');
            $table->string('facility_type', 20)->nullable()->after('type');
            $table->text('address')->nullable()->after('facility_type');
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('email_institution', 255)->nullable()->after('phone');
            $table->string('website', 255)->nullable()->after('email_institution');
            $table->string('license_number', 100)->nullable()->after('website');
            $table->text('description')->nullable()->after('license_number');

            // Index for filtering by type
            $table->index('type');
            $table->index('facility_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['type', 'facility_type']);
            $table->dropColumn([
                'type',
                'facility_type',
                'address',
                'phone',
                'email_institution',
                'website',
                'license_number',
                'description',
            ]);
        });
    }
};

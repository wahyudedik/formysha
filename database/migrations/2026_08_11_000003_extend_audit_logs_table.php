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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('connection_id')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('event');
            $table->string('permission', 20)->nullable()->after('description');
            $table->foreign('connection_id')->references('id')->on('connections')->nullOnDelete();
            $table->index('connection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['connection_id']);
            $table->dropIndex(['connection_id']);
            $table->dropColumn(['connection_id', 'description', 'permission']);
        });
    }
};

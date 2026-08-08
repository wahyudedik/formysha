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
        Schema::table('tenant_brandings', function (Blueprint $table) {
            $table->text('login_heading')->nullable()->after('organization_name');
            $table->text('login_subheading')->nullable()->after('login_heading');
            $table->text('footer_text')->nullable()->after('accent_color');
            $table->string('email_sender_name', 100)->nullable()->after('footer_text');
            $table->string('email_sender_email', 100)->nullable()->after('email_sender_name');
            $table->boolean('is_white_label_enabled')->default(false)->after('email_sender_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_brandings', function (Blueprint $table) {
            $table->dropColumn([
                'login_heading',
                'login_subheading',
                'footer_text',
                'email_sender_name',
                'email_sender_email',
                'is_white_label_enabled',
            ]);
        });
    }
};

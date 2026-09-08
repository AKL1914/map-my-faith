<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->string('contact_name', 100)->nullable()->after('notes');
            $table->string('contact_phone', 20)->nullable()->after('contact_name');
            $table->string('contact_email', 255)->nullable()->after('contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'contact_email']);
        });
    }
};

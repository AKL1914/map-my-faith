<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->index(
                ['campaign_id', 'latitude', 'longitude'],
                'idx_pins_campaign_latitude_longitude'
            );
        });
    }

    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->dropIndex('idx_pins_campaign_latitude_longitude');
        });
    }
};

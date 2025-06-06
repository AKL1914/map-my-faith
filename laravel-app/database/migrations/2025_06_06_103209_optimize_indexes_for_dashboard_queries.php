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
        Schema::table('users', function (Blueprint $table) {
            // Ensure 'id' is primary (already is by default)
            // Index for WHERE name != 'Admin'
            $table->index('name', 'idx_users_name');

            // Index for GROUP BY and filtering
            $table->index('area', 'idx_users_area');

            // Index for JOINs — 'id' is already primary, adding for email filtering if used
            $table->index('email', 'idx_users_email'); // matches 'users_email_unique'
        });

        Schema::table('pins', function (Blueprint $table) {
            // Index used in JOINs
            $table->index('user_id', 'idx_pins_user_id');

            // Indexes for filtering
            $table->index('campaign_id', 'idx_pins_campaign_id');
            $table->index('created_at', 'idx_pins_created_at');

            // Composite index to optimize GROUP BY/ORDER BY if used together
            $table->index(['campaign_id', 'created_at'], 'idx_pins_campaign_created');

            // Optional: Add a covering index for reports like area-based pin counts
            $table->index(['user_id', 'campaign_id', 'created_at'], 'idx_pins_user_campaign_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_area');
            $table->dropIndex('idx_users_email');
        });

        Schema::table('pins', function (Blueprint $table) {
            $table->dropIndex('idx_pins_user_id');
            $table->dropIndex('idx_pins_campaign_id');
            $table->dropIndex('idx_pins_created_at');
            $table->dropIndex('idx_pins_campaign_created');
            $table->dropIndex('idx_pins_user_campaign_date');
        });
    }
};

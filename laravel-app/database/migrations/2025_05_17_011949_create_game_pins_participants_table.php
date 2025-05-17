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
        Schema::create('game_pins_participants', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('game_pin_id')->nullable();
            $table->text('distance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_pins_participants');
    }
};

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
        //update admin email
        \App\Models\User::where('email', 'admin@map-share-me.co.nz')
            ->update(['email' => 'mapmyfaith@gmail.com']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::where('email', 'mapmyfaith@gmail.com')
            ->update(['email' => 'admin@map-share-me.co.nz']);
    }
};

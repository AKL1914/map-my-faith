
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('notes', 50)->nullable();
            $table->boolean('is_accepted')->default(true);
            $table->timestamps();
//            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->integer('campaign_id')->nullable();
        });



    }

    public function down(): void
    {
        // Schema::table('pins', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        //     $table->dropForeign(['campaign_id']);
        // });

        Schema::dropIfExists('pins');
    }
};

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
        Schema::create('telegram_cache', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('key');
            $table->string('value');

            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');

            $table->unique(['user_id', 'key']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_cache');
    }
};

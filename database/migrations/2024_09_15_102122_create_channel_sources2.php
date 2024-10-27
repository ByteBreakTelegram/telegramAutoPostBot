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
        Schema::table('channels', function (Blueprint $table) {
            $table->bigInteger('last_public_post_at')->nullable();
        });
        Schema::table('channel_posts', function (Blueprint $table) {
            $table->bigInteger('published_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn('last_public_post_at');
        });
        Schema::table('channel_posts', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};

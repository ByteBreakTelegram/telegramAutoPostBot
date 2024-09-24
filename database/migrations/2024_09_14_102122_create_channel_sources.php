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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название канала
            $table->string('code', 32)->unique()->nullable(); // Уникальный код канала, для определения в какой канал публиковать пост
            $table->bigInteger('telegram_channel_id')->unique(); // Уникальный идентификатор канала
            $table->smallInteger('type_const'); // тип канала, источник публикаций или каналя для публикаций
            $table->smallInteger('status_const'); // Статус
            $table->smallInteger('post_interval_const')->nullable(); // Периодичность публикаций
            $table->boolean('is_business_hours')->default(false); // Только в рабочее время
            $table->string('post_time')->nullable(); // Время публикаций
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
        });

        Schema::create('channel_posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('channel_id');
            $table->bigInteger('telegram_message_id');
            $table->string('telegram_media_group_id')->index()->nullable();
            $table->bigInteger('status_const');
            $table->foreignId('target_channel_id')->nullable()->constrained('channels')->cascadeOnDelete(); // Связь с channels
            $table->bigInteger('target_telegram_message_id')->nullable();

            $table->text('content');
            $table->smallInteger('priority');
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
        });

        Schema::create('channel_post_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_post_id')->constrained('channel_posts')->restrictOnDelete(); // Связь с channel_posts, restrict потому что вначале нужно удалить файлы с диска
            $table->bigInteger('telegram_message_id');
            $table->string('telegram_media_group_id')->nullable();
            $table->string('telegram_file_id'); // ID файла в Telegram
            $table->string('file_type_const'); // Тип файла (например, photo, video, document)
            $table->string('file_path');
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_post_files');
        Schema::dropIfExists('channel_posts');
        Schema::dropIfExists('channels');
    }
};

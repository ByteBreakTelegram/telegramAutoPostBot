<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\Enums\ChannelPostStatus;
use App\Services\ChannelPostPublicService;
use DB;
use Illuminate\Console\Command;

/**
 * Команда для публикаций посто в очереди
 * php artisan channel-post:public
 */
class ChannelPostPublicCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'channel-post:public';

    /**
     * @var string
     */
    protected $description = 'Обработка команд которые приходят от телеграмма'; // Описание команды

    /**
     * Выполнение команды
     */
    public function handle(
        ChannelPostPublicService $channelPostPublicService
    )
    {
        // Текущая дата и время
        $now = now();

        // Запрос к базе данных для получения постов, готовых к публикации
        $query = ChannelPost::query()
            ->with('targetChannel')
            ->join(Channel::tableName(), 'channel_posts.target_channel_id', '=', 'channels.id')
            ->where(
                function ($query) use ($now) {
                    $query->where('channel_posts.status_const', ChannelPostStatus::QUEUE); // Или если была ошибка
                    // Посты, которые еще не были опубликованы или после интервала публикации
                    $query->where(
                        function ($subQuery) {
                            $subQuery->whereNull(
                                'channels.last_public_post_at'
                            ) // Если нет последнего опубликованного поста
                            ->orWhereRaw(
                                'EXTRACT(EPOCH FROM NOW()) - channels.last_public_post_at >= channels.post_interval_const * 3600'
                            ); // Проверка интервала в секундах
                        }
                    );
                }
            )
            ->select('channel_posts.*')
            ->orderBy('channel_posts.priority', 'desc');

        $channelIds = [];
        $query->each(function (ChannelPost $post) use ($channelPostPublicService, &$channelIds, $now) {
            DB::transaction(function () use ($channelPostPublicService, $post, &$channelIds, $now) {
                if (array_key_exists($post->target_channel_id, $channelIds)) {
                    return;
                }
                if ($post->targetChannel->is_business_hours) {
                    // Только будние дни, с 9 до 21 часа
                    if ($now->isWeekday() && $now->hour >= 9 && $now->hour <= 21) {
                        $channelPostPublicService->execute($post);
                    }
                } else {
                    $channelPostPublicService->execute($post);
                }
                $channelIds[$post->target_channel_id] = $post->target_channel_id;
            });
        });
    }
}

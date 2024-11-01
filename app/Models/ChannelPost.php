<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ChannelPostStatus;
use App\Models\Traits\TableName;
use Carbon\Carbon;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * В таблице хранятся посты из каналов источников
 *
 * @property int $id
 * @property int $channel_id
 * @property int $telegram_message_id
 * @property string|null $telegram_media_group_id
 * @property ChannelPostStatus $status_const
 * @property int|null $target_channel_id
 * @property int|null $target_telegram_message_id
 * @property string $content
 * @property int $priority
 * @property Carbon|null $published_at
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *
 * @property Channel $channel
 * @property Channel|null $targetChannel
 * @property Collection<int, ChannelPostFile> $channelPostFiles
 *
 */
class ChannelPost extends Model
{
    use TableName;

    public $table = 'channel_posts';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dateFormat = 'U';


    public $fillable = [
        'channel_id',
        'telegram_message_id',
        'telegram_media_group_id',
        'status_const',
        'target_channel_id',
        'target_telegram_message_id',
        'content',
        'priority',
        'published_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'channel_id' => 'integer',
        'telegram_message_id' => 'integer',
        'telegram_media_group_id' => 'string',
        'status_const' => ChannelPostStatus::class,
        'target_channel_id' => 'integer',
        'target_telegram_message_id' => 'integer',
        'content' => 'string',
        'priority' => 'integer',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function attributeLabels(): array
    {
        return [
            'id' => trans('ID'),
            'channel_id' => trans('Канал источник'),
            'telegram_message_id' => trans('ID сообщения в телеграм'),
            'telegram_media_group_id' => trans('ID группы файлов'),
            'status_const' => trans('Статус'),
            'target_channel_id' => trans('Канал в который идет публикация'),
            'target_telegram_message_id' => trans('Опубликованное сообщение'),
            'content' => trans('Содержимое'),
            'priority' => trans('Приоритет'),
            'published_at' => trans('Дата публикации'),
            'created_at' => trans('Создано'),
            'updated_at' => trans('Обновлено'),
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function targetChannel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function channelPostFiles(): HasMany
    {
        return $this->hasMany(ChannelPostFile::class);
    }
}
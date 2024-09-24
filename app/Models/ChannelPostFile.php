<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ChannelPostFileFileType;
use App\Models\Traits\TableName;
use Carbon\Carbon;
use App\Models\Core\Model;

/**
 * В таблице хранятся файлы постов из каналов
 *
 * @property int $id
 * @property int $channel_post_id
 * @property string $telegram_message_id
 * @property string $telegram_media_group_id
 * @property string $telegram_file_id
 * @property ChannelPostFileFileType $file_type_const
 * @property string $file_path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class ChannelPostFile extends Model
{
    use TableName;

    public $table = 'channel_post_files';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dateFormat = 'U';

    public $fillable = [
        'channel_post_id',
        'telegram_message_id',
        'telegram_media_group_id',
        'telegram_file_id',
        'file_type_const',
        'file_path',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'channel_post_id' => 'integer',
        'telegram_message_id' => 'integer',
        'telegram_media_group_id' => 'string',
        'telegram_file_id' => 'string',
        'file_type_const' => ChannelPostFileFileType::class,
        'file_path' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function attributeLabels(): array
    {
        return [
            'id' => trans('ID'),
            'channel_post_id' => trans('ID поста канала'),
            'telegram_message_id' => trans('ID группы медиа в Telegram'),
            'telegram_media_group_id' => trans('ID группы медиа в Telegram'),
            'telegram_file_id' => trans('ID файла в Telegram'),
            'file_type_const' => trans('Тип файла'),
            'file_path' => trans('Путь к файлу'),
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
}

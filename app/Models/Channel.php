<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ChannelPostInterval;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use App\Models\Traits\TableName;
use Carbon\Carbon;
use App\Models\Core\Model;

/**
 * В таблице хранятся каналы с источником постов и куда посты делать
 *
 * @property int $id
 * @property string $title
 * @property null|string $code
 * @property int $telegram_channel_id
 * @property ChannelType $type_const
 * @property ChannelStatus $status_const
 * @property null|ChannelPostInterval $post_interval_const
 * @property boolean $is_business_hours
 * @property null|string $post_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Channel extends Model
{
    use TableName;

    public $table = 'channels';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dateFormat = 'U';

    public $fillable = [
        'title',
        'code',
        'telegram_channel_id',
        'type_const',
        'status_const',
        'post_interval_const',
        'is_business_hours',
        'post_time',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'code' => 'string',
        'telegram_channel_id' => 'integer',
        'type_const' => ChannelType::class,
        'status_const' => ChannelStatus::class,
        'post_interval_const' => ChannelPostInterval::class,
        'is_business_hours' => 'bool',
        'post_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function attributeLabels(): array
    {
        return [
            'id' => trans('ID'),
            'title' => trans('Название'),
            'code' => trans('Уникальный код канала'),
            'telegram_channel_id' => trans('ID канала'),
            'type_const' => trans('Тип канала'),
            'status_const' => trans('Статус'),
            'post_interval_const' => trans('Интервал публикаций'),
            'is_business_hours' => trans('Только в рабочие часы'),
            'post_time' => trans('Время публикаций через точку с запятой'),
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
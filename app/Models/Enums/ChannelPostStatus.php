<?php

namespace App\Models\Enums;

use App\Models\Enums\Core\HasLabel;

enum ChannelPostStatus: int implements HasLabel
{
    /** В очереди на публикацию */
    case QUEUE  = 1;
    /** Опубликовно */

    case PUBLISHED = 2;
    /** Ошибка публикации */
    case ERROR = 3;
    /** Отменен */
    case CANCELLED = 4;
    /** На паузе, например редактировали пост и появилась ошибка */
    case PAUSED  = 5;


    public function label(): string
    {
        return match ($this) {
            self::QUEUE => trans('В ожидании'),
            self::PUBLISHED => trans('Опубликован'),
            self::ERROR => trans('Ошибка'),
            self::PAUSED => trans('Приостановлен'),
        };
    }
}

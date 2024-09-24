<?php

namespace App\Models\Enums;

use App\Models\Enums\Core\HasLabel;

enum ChannelStatus: int implements HasLabel
{
    case ACTIVE = 1;
    case PAUSED = 2;
    case DELETED = 3;


    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => trans('Активен'),
            self::PAUSED => trans('Приостановлен'),
            self::DELETED => trans('Удален'),
        };
    }
}

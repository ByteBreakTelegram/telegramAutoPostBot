<?php

namespace App\Models\Enums;

use App\Models\Enums\Core\HasLabel;

enum ChannelType: int implements HasLabel
{
    case SOURCE = 1;
    case TARGET = 2;


    public function label(): string
    {
        return match ($this) {
            self::SOURCE => trans('Источник публикаций'),
            self::TARGET => trans('Для публикаций'),
        };
    }
}

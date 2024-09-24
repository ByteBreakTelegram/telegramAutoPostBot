<?php

namespace App\Models\Enums;

use App\Models\Enums\Core\HasLabel;

enum ChannelPostInterval: int implements HasLabel
{
    case SIX_HOURS = 6;
    case TWELVE_HOURS = 12;
    case ONE_DAY = 24;
    case THREE_DAYS = 72;
    case SEVEN_DAYS = 168;


    public function label(): string
    {
        return match ($this) {
            self::SIX_HOURS => trans('6 Hours'),
            self::TWELVE_HOURS => trans('12 Hours'),
            self::ONE_DAY => trans('1 Day'),
            self::THREE_DAYS => trans('3 Days'),
            self::SEVEN_DAYS => trans('7 Days'),
        };
    }
}

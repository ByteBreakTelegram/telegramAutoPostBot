<?php

namespace App\Models\Enums;


use App\Models\Enums\Core\HasLabel;

enum UserRole: int implements HasLabel
{
    case USER = 1;
    case ADMIN = 943;

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::USER => trans('Пользователь'),
            self::ADMIN => trans('Админ'),
        };
    }
}

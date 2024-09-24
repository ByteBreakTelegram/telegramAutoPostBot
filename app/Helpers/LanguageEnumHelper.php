<?php

declare(strict_types=1);


namespace App\Helpers;



use App\Models\Enums\Core\HasLabel;

enum LanguageEnumHelper: string implements HasLabel
{
    case RU = 'ru';
    case EN = 'en';

    public function label(): string
    {
        return match ($this) {
            self::RU => trans('Русский'),
            self::EN => trans('Английский'),
        };
    }

    public static function scalar(): array
    {
        return [
            self::RU->value,
            self::EN->value,
        ];
    }
}
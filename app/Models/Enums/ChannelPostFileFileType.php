<?php

namespace App\Models\Enums;

use App\Models\Enums\Core\HasLabel;

enum ChannelPostFileFileType: string implements HasLabel
{
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case PHOTO = 'photo';
    case DOCUMENT = 'document';

    /**
     * Получить метку для значения перечисления
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIDEO => trans('Видео'),
            self::AUDIO => trans('Аудио'),
            self::PHOTO => trans('Фото'),
            self::DOCUMENT => trans('Документ'),
        };
    }
}

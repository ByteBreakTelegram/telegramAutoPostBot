<?php

declare(strict_types=1);

namespace App\Telegram\Adapters\Dto;

use App\Components\Dto\Dto;

/**
 * DTO для хранения контента сообщения Telegram,
 * который будет использоваться для публикации в канале.
 */
class TelegramChannelPostAdaptorContentDto extends Dto
{
    /**
     * Исходный текст сообщения, как он получен из Telegram.
     * @var string|null
     */
    public ?string $raw;

    /**
     * Обработанный текст поста, который будет опубликован.
     * @var string|null
     */
    public ?string $text;

    /**
     * Приоритет публикации поста.
     * Более высокий приоритет означает, что пост будет опубликован раньше.
     * @var int|null
     */
    public ?int $priority;

    /**
     * Код канала, в который должен быть опубликован пост.
     * Этот код извлекается из текста сообщения.
     * @var string|null
     */
    public ?string $codeChannel;
    /**
     * Код канала, в который должен быть опубликован пост.
     * Этот код извлекается из текста сообщения.
     * @var int|null
     */
    public ?int $targetChannelId;
}
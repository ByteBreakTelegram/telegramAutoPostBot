<?php

declare(strict_types=1);

namespace App\Telegram\Adapters\Dto;

use App\Components\Dto\Dto;
use App\Models\ChannelPost;

/**
 * DTO для адаптации сообщения Telegram и хранения связанных данных,
 * включая ошибки и информацию о канале.
 */
class TelegramChannelPostAdaptorDto extends Dto
{
    /**
     * Массив ошибок, возникающих в процессе обработки сообщения.
     * @var array
     */
    public array $errors;

    /**
     * Идентификатор канала, из которого пришло сообщение.
     * @var int
     */
    public int $channelId;

    /**
     * Контент сообщения, включая текст и другие данные.
     * @var TelegramChannelPostAdaptorContentDto
     */
    public TelegramChannelPostAdaptorContentDto $content;

    /**
     * пост канала, если он существует в базе данных.
     * @var ChannelPost|null
     */
    public ?ChannelPost $channelPost;

    /**
     * Идентификатор сообщения в Telegram.
     * @var int
     */
    public int $telegramMessageId;

    /**
     * Идентификатор группы медиа, если сообщение является частью группы.
     * @var string|null
     */
    public ?string $mediaGroupId;

    /**
     * Идентификатор целевого канала, куда будет опубликован пост.
     * @var int|null
     */
    public ?int $targetChannelId;
}
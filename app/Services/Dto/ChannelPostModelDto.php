<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelPostStatus;

class ChannelPostModelDto extends Dto
{
    public int $id;
    public int $channel_id;
    public int $telegram_message_id;
    public ?string $telegram_media_group_id;
    public ChannelPostStatus $status_const;
    public ?int $target_channel_id;
    public int $target_telegram_message_id;
    public string $content;
    public int $priority;
}
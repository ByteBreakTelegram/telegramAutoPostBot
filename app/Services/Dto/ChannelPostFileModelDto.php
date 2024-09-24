<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelPostFileFileType;

class ChannelPostFileModelDto extends Dto
{
    public int $id;
    public int $channel_post_id;
    public string $telegram_message_id;
    public string $telegram_media_group_id;
    public string $telegram_file_id;
    public ChannelPostFileFileType $file_type_const;
    public string $file_path;
}
<?php

declare(strict_types=1);


namespace App\Repositories\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;

class ChannelPostFileFilter extends Dto
{
    public int $channel_post_id;
    public int $telegram_message_id;
}
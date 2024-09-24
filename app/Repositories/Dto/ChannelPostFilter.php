<?php

declare(strict_types=1);


namespace App\Repositories\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;

class ChannelPostFilter extends Dto
{
    /** @var int[] */
    public array $telegram_message_ids;
    /** @var string[] */
    public array $telegram_media_group_ids;
}
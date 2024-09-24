<?php

declare(strict_types=1);


namespace App\Repositories\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;

class ChannelFilter extends Dto
{
    /** @var string[] */
    public array $codes;
    /** @var ChannelType[] */
    public array $type_consts;
    /** @var ChannelStatus[] */
    public array $status_consts;
    /** @var int[] */
    public array $telegram_channel_ids;
    /** @var string[] */
    public array $telegram_media_group_ids;
}
<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;

class ChannelSettingDto extends Dto
{
    public int $channelId;
    public bool $isSettingTimePublic;

    public int $status_const;
    public int $type_const;
    public int $post_interval_const;
    public bool $is_business_hours;
}
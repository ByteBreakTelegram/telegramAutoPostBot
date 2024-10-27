<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;
use App\Models\Enums\ChannelPostInterval;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use Carbon\Carbon;

class ChannelModelDto extends Dto
{
    public int $id;
    public string $title;
    public string $code;
    public int $telegram_channel_id;
    public ChannelType $type_const;
    public ChannelStatus $status_const;
    public ?ChannelPostInterval $post_interval_const;
    public bool $is_business_hours;
    public ?string $post_time;
    public ?Carbon $last_public_post_at;
}
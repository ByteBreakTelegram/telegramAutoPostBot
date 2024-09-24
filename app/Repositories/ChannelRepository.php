<?php

declare(strict_types=1);


namespace App\Repositories;


use App\Models\Channel;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use App\Repositories\Core\Repository;
use App\Repositories\Dto\ChannelFilter;
use Illuminate\Database\Eloquent\Builder;


class ChannelRepository extends Repository
{

    public function findByFilter(ChannelFilter $filter): Builder
    {
        return Channel::query()
            ->when($filter->keyExist('codes'), function (Builder $q) use ($filter) {
                $q->whereIn('code', $filter->codes);
            })
            ->when($filter->keyExist('type_consts'), function (Builder $q) use ($filter) {
                $q->whereIn('type_const', $filter->type_consts);
            })
            ->when($filter->keyExist('status_consts'), function (Builder $q) use ($filter) {
                $q->whereIn('status_const', $filter->status_consts);
            })
            ->when($filter->keyExist('telegram_channel_ids'), function (Builder $q) use ($filter) {
                $q->whereIn('telegram_channel_id', $filter->telegram_channel_ids);
            })
            ->when($filter->keyExist('telegram_media_group_ids'), function (Builder $q) use ($filter) {
                $q->whereIn('telegram_media_group_id', $filter->telegram_media_group_ids);
            });
    }


    /**
     * Это массив кодов каналов в которые публикуем посты ботом
     * @return array
     */
    public function getPublishChannelCodes(): array
    {
        $channelFilter = new ChannelFilter();
        $channelFilter->type_consts = [ChannelType::TARGET];
        $channelFilter->status_consts = [ChannelStatus::ACTIVE, ChannelStatus::PAUSED];
        return $this->findByFilter($channelFilter)
            ->pluck('code')
            ->toArray();
    }
}
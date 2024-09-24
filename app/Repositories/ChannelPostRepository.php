<?php

declare(strict_types=1);


namespace App\Repositories;


use App\Models\ChannelPost;
use App\Repositories\Core\Repository;
use App\Repositories\Dto\ChannelPostFilter;
use Illuminate\Database\Eloquent\Builder;

class ChannelPostRepository extends Repository
{

    public function findByFilter(ChannelPostFilter $filter): Builder
    {
        return ChannelPost::query()
            ->when($filter->keyExist('telegram_message_ids'), function (Builder $q) use ($filter) {
                $q->whereIn('telegram_message_id', $filter->telegram_message_ids);
            })
            ->when($filter->keyExist('telegram_media_group_ids'), function (Builder $q) use ($filter) {
                $q->whereIn('telegram_media_group_id', $filter->telegram_media_group_ids);
            });
    }
}
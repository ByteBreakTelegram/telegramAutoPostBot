<?php

declare(strict_types=1);


namespace App\Repositories;


use App\Models\ChannelPostFile;
use App\Repositories\Core\Repository;
use App\Repositories\Dto\ChannelPostFileFilter;
use Illuminate\Database\Eloquent\Builder;

class ChannelPostFileRepository extends Repository
{

    public function findByFilter(ChannelPostFileFilter $filter): Builder
    {
        return ChannelPostFile::query()
            ->when($filter->keyExist('channel_post_id'), function (Builder $q) use ($filter) {
                $q->where('channel_post_id', $filter->channel_post_id);
            })
            ->when($filter->keyExist('telegram_message_id'), function (Builder $q) use ($filter) {
                $q->where('telegram_message_id', $filter->telegram_message_id);
            });
    }
}
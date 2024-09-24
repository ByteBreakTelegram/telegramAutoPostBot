<?php

declare(strict_types=1);


namespace App\Services;


use App\Models\Channel;
use App\Models\ChannelPost;
use App\Services\Dto\ChannelModelDto;
use App\Services\Dto\ChannelPostModelDto;

class ChannelPostModelService
{


    public function create(ChannelPostModelDto $dto): ChannelPost
    {
        // TODO тут должна быть базовая валидация при создании


        $model = new ChannelPost();
        $model->fill(
            $dto->toArray(
                [
                    'channel_id',
                    'telegram_message_id',
                    'telegram_media_group_id',
                    'status_const',
                    'target_channel_id',
                    'target_telegram_message_id',
                    'content',
                    'priority',
                ]
            )
        );
        $model->saveOrException();

        return $model;
    }

    public function update(ChannelPost $model, ChannelPostModelDto $dto): ChannelPost
    {
        // TODO тут должна быть базовая валидация при редактировании


        $model->fill(
            $dto->toArray(
                [
                    'channel_id',
                    'telegram_message_id',
                    'telegram_media_group_id',
                    'status_const',
                    'target_channel_id',
                    'target_telegram_message_id',
                    'content',
                    'priority',
                ]
            )
        );
        $model->saveOrException();
        return $model;
    }
}
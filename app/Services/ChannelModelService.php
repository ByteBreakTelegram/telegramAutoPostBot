<?php

declare(strict_types=1);


namespace App\Services;


use App\Models\Channel;
use App\Services\Dto\ChannelModelDto;

class ChannelModelService
{


    public function create(ChannelModelDto $dto): Channel
    {
        // TODO тут должна быть базовая валидация при создании


        $model = new Channel();
        $model->fill(
            $dto->toArray(
                [
                    'title',
                    'code',
                    'telegram_channel_id',
                    'type_const',
                    'status_const',
                    'post_interval_const',
                    'is_business_hours',
                    'post_time',
                ]
            )
        );
        $model->saveOrException();

        return $model;
    }

    public function update(Channel $model, ChannelModelDto $dto): Channel
    {
        // TODO тут должна быть базовая валидация при редактировании


        $model->fill(
            $dto->toArray(
                [
                    'title',
                    'code',
                    'telegram_channel_id',
                    'type_const',
                    'status_const',
                    'post_interval_const',
                    'is_business_hours',
                    'post_time',
                ]
            )
        );
        $model->saveOrException();
        return $model;
    }
}
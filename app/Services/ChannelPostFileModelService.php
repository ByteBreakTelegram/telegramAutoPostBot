<?php

declare(strict_types=1);


namespace App\Services;


use App\Models\ChannelPostFile;
use App\Services\Dto\ChannelPostFileModelDto;

class ChannelPostFileModelService
{


    public function create(ChannelPostFileModelDto $dto): ChannelPostFile
    {
        // TODO тут должна быть базовая валидация при создании


        $model = new ChannelPostFile();
        $model->fill(
            $dto->toArray(
                [
                    'channel_post_id',
                    'telegram_media_group_id',
                    'telegram_file_id',
                    'file_type_const',
                    'file_path',
                ]
            )
        );
        $model->saveOrException();

        return $model;
    }

    public function update(ChannelPostFile $model, ChannelPostFileModelDto $dto): ChannelPostFile
    {
        // TODO тут должна быть базовая валидация при редактировании


        $model->fill(
            $dto->toArray(
                [
                    'channel_post_id',
                    'telegram_media_group_id',
                    'telegram_file_id',
                    'file_type_const',
                    'file_path',
                ]
            )
        );
        $model->saveOrException();
        return $model;
    }


    public function delete(ChannelPostFile $model): void
    {
        $model->delete();
    }
}
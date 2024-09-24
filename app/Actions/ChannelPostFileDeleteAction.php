<?php

declare(strict_types=1);

namespace App\Actions;


use App\Models\ChannelPostFile;
use App\Services\ChannelPostFileModelService;
use Illuminate\Support\Facades\Storage;


/**
 * Удаление файла из базы и диска
 */
class ChannelPostFileDeleteAction
{

    public function __construct(
        protected readonly ChannelPostFileModelService $channelPostFileModelService,
    )
    {
    }

    public function execute(ChannelPostFile $model): void
    {
        // Удаляем файл с диска
        if (Storage::exists($model->file_path)) {
            Storage::delete($model->file_path);
        }
        // Удаляем файл из базы
        $this->channelPostFileModelService->delete($model);
    }
}
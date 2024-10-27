<?php

declare(strict_types=1);


namespace App\Services;


use App\Components\Telegram\InputMediaAudio;
use App\Models\ChannelPost;
use App\Models\ChannelPostFile;
use App\Models\Enums\ChannelPostFileFileType;
use App\Models\Enums\ChannelPostStatus;
use App\Services\Dto\ChannelModelDto;
use App\Services\Dto\ChannelPostModelDto;
use App\Services\Dto\CreatePostFromTelegramResultDto;
use Illuminate\Database\Eloquent\Collection;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia;
use TelegramBot\Api\Types\InputMedia\InputMediaPhoto;
use TelegramBot\Api\Types\InputMedia\InputMediaVideo;

/**
 * Сервис публикации поста в телеграм канал
 */
readonly class ChannelPostPublicService
{

    /**
     * @param Client|BotApi $botApi
     */
    public function __construct(
        private Client $botApi,
        private ChannelModelService $channelModelService,
        private ChannelPostModelService $channelPostModelService,
    ) {
    }

    public function execute(ChannelPost $channelPost): void
    {
        $message = null;
        /** @var Collection<int, ChannelPostFile> $files */
        $files = $channelPost->channelPostFiles()->orderByDesc('id')->get();
        if (!$files->isEmpty()) {
            $i = 0;
            $media = new ArrayOfInputMedia();
            foreach ($files as $file) {
                // Получаем содержимое файла
                $fileContent = $file->getFullPath();
                if ($i === 0) {
                    $content = $channelPost->content;
                } else {
                    $content = null;
                }
                // Проверяем тип файла
                switch ($file->file_type_const) {
                    case ChannelPostFileFileType::PHOTO:
                        $media[] = new InputMediaPhoto($file->telegram_file_id, $content);
                        break;

                    case ChannelPostFileFileType::VIDEO:
                        $media[] = new InputMediaVideo($file->telegram_file_id, $content);
                        break;

                    case ChannelPostFileFileType::AUDIO:
                        $media[] = new InputMediaAudio($file->telegram_file_id, $content);
                        break;

                    default:
                        // Обработка других типов файлов (если нужно)
                        break;
                }
                $i++;
            }

            $message = $this->botApi->sendMediaGroup($channelPost->targetChannel->telegram_channel_id, $media);
        } else {
            //Только текст
            $message = $this->botApi->sendMessage(
                $channelPost->targetChannel->telegram_channel_id,
                $channelPost->content
            );
        }

        if ($message) {
            if (is_array($message)) {
                $message = reset($message); // Получаем первый элемент
            }

            $channelPostModelDto = new ChannelPostModelDto();
            $channelPostModelDto->target_telegram_message_id = $message->getMessageId();
            $channelPostModelDto->published_at = now();
            $channelPostModelDto->status_const = ChannelPostStatus::PUBLISHED;
            $this->channelPostModelService->update($channelPost, $channelPostModelDto);

            $channelModelDto = new ChannelModelDto();
            $channelModelDto->last_public_post_at = now();
            $this->channelModelService->update($channelPost->targetChannel, $channelModelDto);


            // В канале с источником постов отмечаем пост как отправленныый
            $result = new CreatePostFromTelegramResultDto();
            $result->codeChannel = $channelPost->targetChannel->code;
            $result->postText = $channelPost->content;
            $result->priority = $channelPost->priority;
            $result->postStatus = $channelPost->status_const->label();

            if (!$files->isEmpty()) {
                $this->botApi->editMessageCaption(
                    $channelPost->channel->telegram_channel_id,
                    $channelPost->telegram_message_id,
                    $result->getTextForTelegram()
                );
            } else {
                $this->botApi->editMessageText(
                    $channelPost->channel->telegram_channel_id,
                    $channelPost->telegram_message_id,
                    $result->getTextForTelegram()
                );
            }


        }
    }
}
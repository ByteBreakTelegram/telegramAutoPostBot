<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\ChannelPostFileDeleteAction;
use App\Models\ChannelPost;
use App\Models\ChannelPostFile;
use App\Models\Enums\ChannelPostFileFileType;
use App\Models\Enums\ChannelPostStatus;
use App\Repositories\ChannelPostFileRepository;
use App\Repositories\ChannelPostRepository;
use App\Repositories\Dto\ChannelPostFileFilter;
use App\Repositories\Dto\ChannelPostFilter;
use App\Services\Dto\ChannelPostModelDto;
use App\Services\Dto\CreatePostFromTelegramResultDto;
use App\Telegram\Adapters\Dto\TelegramChannelPostAdaptorDto;
use App\Telegram\Adapters\TelegramChannelPostAdaptor;
use Exception;
use Illuminate\Support\Facades\Storage;
use Str;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;

/**
 * Класс для сохранения постов из Telegram в базу данных.
 * 1. Валидирует входящие данные.
 * 2. Сохраняет посты и файлы.
 * 3. Обновляет статус поста.
 */
readonly class CreatePostFromTelegramService
{
    public function __construct(
        private ChannelPostModelService $channelPostModelService,
        private ChannelPostRepository $channelPostRepository,
        private ChannelPostFileDeleteAction $channelPostFileDeleteAction,
        private TelegramChannelPostAdaptor $telegramChannelPostAdaptor,
        private ChannelPostFileRepository $channelPostFileRepository,
        private Client $botApi,
    ) {}

    /**
     * Основной метод для обработки сообщений из Telegram.
     *
     * @throws Exception
     */
    public function execute(Message $message): CreatePostFromTelegramResultDto
    {
        $result = new CreatePostFromTelegramResultDto();

        // Адаптация сообщения Telegram к DTO
        $telegramChannelPostAdaptorDto = $this->telegramChannelPostAdaptor->execute($message);
        if (!$telegramChannelPostAdaptorDto) {
            return $result;
        }

        $channelPost = null;
        $mediaGroupId = $message->getMediaGroupId();

        // Поиск существующего поста по mediaGroupId
        if ($mediaGroupId) {
            $channelFilter = new ChannelPostFilter();
            $channelFilter->telegram_media_group_ids = [$mediaGroupId];
            $channelPost = $this->channelPostRepository->findByFilter($channelFilter)->orderBy('id')->first();
        }

        // Если пост не найден, создаем или обновляем его
        $channelPostModelDto = $this->getChannelPostModelDto($telegramChannelPostAdaptorDto);
        $channelPost = $telegramChannelPostAdaptorDto->channelPost
            ? $this->channelPostModelService->update($telegramChannelPostAdaptorDto->channelPost, $channelPostModelDto)
            : $this->channelPostModelService->create($channelPostModelDto);

        // Синхронизируем файлы, прикрепленные к посту
        $this->syncFiles($channelPost, $message);

        // Обработка ошибок
        if ($telegramChannelPostAdaptorDto->keyExist('errors')) {
            $result->errors = $telegramChannelPostAdaptorDto->errors;
        }
        // Возвращаем основную информацию для последующего использования
        $result->codeChannel = $telegramChannelPostAdaptorDto->channelPost?->targetChannel?->code ?? 'simpleCodeChannel';
        $result->postText = $telegramChannelPostAdaptorDto->content->text ?? '';
        $result->priority = $telegramChannelPostAdaptorDto->content->priority ?? 0;
        $result->postStatus = $channelPost->status_const->label();

        return $result;
    }

    /**
     * Синхронизация файлов с постом. Если файлы уже существуют — они перезаписываются.
     *
     * @param ChannelPost $channelPost
     * @param Message $message
     */
    private function syncFiles(ChannelPost $channelPost, Message $message): void
    {
        $fileData = [];
        $channelPostFileFilter = new ChannelPostFileFilter();
        $channelPostFileFilter->channel_post_id = $channelPost->id;
        $channelPostFileFilter->telegram_message_id = $message->getMessageId();

        // Проверяем, есть ли уже файл для этого поста, и удаляем его, если он существует
        $channelPostFile = $this->channelPostFileRepository->findByFilter($channelPostFileFilter)->first();
        if ($channelPostFile) {
            $this->channelPostFileDeleteAction->execute($channelPostFile);
        }

        // Сохранение фото (выбираем самое большое фото из массива)
        if ($message->getPhoto()) {
            $photos = $message->getPhoto();
            $largestPhoto = end($photos);
            $fileData[$largestPhoto->getFileId()]['file_path'] = $this->saveTelegramFile($largestPhoto->getFileId(), 'photos/');
            $fileData[$largestPhoto->getFileId()]['file_type_const'] = ChannelPostFileFileType::PHOTO;
        }

        // Сохранение видео
        if ($message->getVideo()) {
            $video = $message->getVideo();
            $fileData[$video->getFileId()]['file_path'] = $this->saveTelegramFile($video->getFileId(), 'videos/');
            $fileData[$video->getFileId()]['file_type_const'] = ChannelPostFileFileType::VIDEO;
        }

        // Сохранение документов
        if ($message->getDocument()) {
            $document = $message->getDocument();
            $fileData[$document->getFileId()]['file_path'] = $this->saveTelegramFile($document->getFileId(), 'documents/');
            $fileData[$document->getFileId()]['file_type_const'] = ChannelPostFileFileType::DOCUMENT;
        }

        // Сохранение аудио
        if ($message->getAudio()) {
            $audio = $message->getAudio();
            $fileData[$audio->getFileId()]['file_path'] = $this->saveTelegramFile($audio->getFileId(), 'audio/');
            $fileData[$audio->getFileId()]['file_type_const'] = ChannelPostFileFileType::AUDIO;
        }

        // Сохранение всех файлов в базу данных
        foreach ($fileData as $fileId => $fileItem) {
            ChannelPostFile::create([
                                        'channel_post_id' => $channelPost->id,
                                        'telegram_message_id' => $message->getMessageId(),
                                        'telegram_media_group_id' => $message->getMediaGroupId(),
                                        'telegram_file_id' => $fileId,
                                        'file_type_const' => $fileItem['file_type_const'],
                                        'file_path' => $fileItem['file_path'],
                                    ]);
        }
    }

    /**
     * Вспомогательная функция для загрузки и сохранения файла.
     *
     * @param string $fileId ID файла в Telegram
     * @param string $folder Папка для сохранения файла
     * @return string Путь к сохраненному файлу
     */
    private function saveTelegramFile(string $fileId, string $folder): string
    {
        // Получение файла из API Telegram
        $file = $this->botApi->getFile($fileId);
        $filePath = $file->getFilePath();

        // Формирование URL для скачивания файла
        $fileUrl = 'https://api.telegram.org/file/bot' . config('telegram.client.token') . '/' . $filePath;

        // Скачивание содержимого файла
        $contents = file_get_contents($fileUrl);

        // Генерация уникального имени файла с помощью UUID
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $uuid = Str::uuid()->toString();
        $subdir = substr($uuid, 0, 2); // Создание поддиректории из первых двух символов UUID

        // Полный путь к файлу
        $directory = $folder . $subdir;
        $fileName = $directory . '/' . $uuid . '.' . $extension;

        // Создание директории, если она не существует, с правами 0777
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
            chmod(Storage::path($directory), 0777);
        }

        // Сохранение файла в хранилище Laravel
        Storage::put($fileName, $contents);
        return $fileName;
    }

    /**
     * Подготавливает DTO для сохранения данных о посте.
     *
     * @param TelegramChannelPostAdaptorDto $dto
     * @return ChannelPostModelDto
     * @throws Exception
     */
    private function getChannelPostModelDto(TelegramChannelPostAdaptorDto $dto): ChannelPostModelDto
    {
        $result = new ChannelPostModelDto();
        $result->channel_id = $dto->channelId;
        $result->telegram_message_id = $dto->telegramMessageId;
        $result->telegram_media_group_id = $dto->mediaGroupId;

        // Устанавливаем статус поста в зависимости от наличия ошибок
        $result->status_const = $dto->errors === [] ? ChannelPostStatus::QUEUE : ChannelPostStatus::PAUSED;

        // Присваиваем контент и приоритет
        $result->target_channel_id = $dto->targetChannelId;
        $result->content = $dto->content->text ?? '';
        $result->priority = $dto->content->priority ?? 0;

        return $result;
    }
}

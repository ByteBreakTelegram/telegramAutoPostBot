<?php

declare(strict_types=1);

namespace App\Telegram\Adapters;

use App\Helpers\StringHelper;
use App\Models\Enums\ChannelPostInterval;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use App\Repositories\ChannelPostRepository;
use App\Repositories\ChannelRepository;
use App\Repositories\Dto\ChannelFilter;
use App\Repositories\Dto\ChannelPostFilter;
use App\Services\ChannelModelService;
use App\Services\Dto\ChannelModelDto;
use App\Telegram\Adapters\Dto\TelegramChannelPostAdaptorContentDto;
use App\Telegram\Adapters\Dto\TelegramChannelPostAdaptorDto;
use Exception;
use TelegramBot\Api\Types\Message;

/**
 * Адаптер для обработки сообщений Telegram и подготовки DTO для публикации в канале
 */
readonly class TelegramChannelPostAdaptor
{
    public function __construct(
        private ChannelRepository $channelRepository,
        private ChannelModelService $channelModelService,
        private ChannelPostRepository $channelPostRepository,
    ) {}

    /**
     * Основной метод адаптера. Обрабатывает сообщение и возвращает DTO с данными для публикации
     */
    public function execute(Message $message): ?TelegramChannelPostAdaptorDto
    {
        $result = new TelegramChannelPostAdaptorDto();
        $result->errors = [];
        $result->targetChannelId = null;

        // Поиск канала в базе данных по telegram_channel_id
        $channelFilter = new ChannelFilter();
        $channelFilter->telegram_channel_ids = [$message->getChat()->getId()];
        $channel = $this->channelRepository->findByFilter($channelFilter)->first();

        // Если канал не найден — создаем новый канал
        if (!$channel) {
            // Первый добавленный канал делаем источников, остальные для публикаций
            $channelFilter = new ChannelFilter();
            $isFirst = !$this->channelRepository->findByFilter($channelFilter)->exists();

            $channelModelDto = new ChannelModelDto();
            $channelModelDto->title = $message->getChat()->getTitle();
            $channelModelDto->telegram_channel_id = $message->getChat()->getId();
            $channelModelDto->type_const = $isFirst ? ChannelType::SOURCE : ChannelType::TARGET;
            $channelModelDto->status_const = $isFirst ? ChannelStatus::ACTIVE : ChannelStatus::PAUSED;
            $channelModelDto->post_interval_const = $isFirst ? null : ChannelPostInterval::THREE_DAYS;
            $channelModelDto->post_time = $isFirst ? null : '12:30';
            $channelModelDto->code = StringHelper::transliterate($channelModelDto->title);
            $channel = $this->channelModelService->create($channelModelDto);
        }
        $result->channelId = $channel->id;

        // Если канал не является источником, возвращаем null
        if ($channel->type_const !== ChannelType::SOURCE) {
            return null;
        }

        // Получаем текст сообщения или заголовок прикрепленного файла
        $text = $message->getText() ?? $message->getCaption();
        $result->content = $this->parseText($text, $result);

        $result->telegramMessageId = $message->getMessageId();
        $result->mediaGroupId = $message->getMediaGroupId();

        // Поиск поста по telegram_message_id
        $channelPostFilter = new ChannelPostFilter();
        $channelPostFilter->telegram_message_ids = [$result->telegramMessageId];
        $channelPost = $this->channelPostRepository->findByFilter($channelPostFilter)->first();
        $result->channelPost = $channelPost;
        if ($result->content->keyExist('targetChannelId')) {
            $result->targetChannelId = $result->content->targetChannelId;
        }
        return $result;
    }

    /**
     * Парсинг текста сообщения и подготовка DTO контента
     *
     * @param string|null $text
     * @param TelegramChannelPostAdaptorDto $telegramChannelPostAdaptorDto
     * @return TelegramChannelPostAdaptorContentDto
     * @throws Exception
     */
    private function parseText(?string $text, TelegramChannelPostAdaptorDto $telegramChannelPostAdaptorDto): TelegramChannelPostAdaptorContentDto
    {
        $result = new TelegramChannelPostAdaptorContentDto();

        if ($text === null) {
            $telegramChannelPostAdaptorDto->errors[] = 'Текст поста не указан';
            return $result;
        }

        $result->raw = $text;
        $lines = explode("\n", $text);

        // Проверяем наличие символа `_`, разделяющего технические и текстовые данные
        $hasUnderscore = array_reduce($lines, fn($carry, $line) => $carry || trim($line) === '_', false);
        if (!$hasUnderscore) {
            array_unshift($lines, '_');
            array_unshift($lines, 'simpleCodeChannel');
        }
        $isOnlyPost = false;
        $postText = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($isOnlyPost) {
                $postText[] = $line;
            } else {
                if ($line === '_') {
                    $isOnlyPost = true; // Начинается текст поста
                } elseif (!$result->keyExist('codeChannel') && $line !== '') {
                    $result->codeChannel = $line; // Первая строка — это код канала
                } elseif (stripos($line, 'приоритет:') !== false) {
                    $result->priority = (int)preg_replace('/\D/', '', $line); // Забираем приоритет
                }
            }
        }
        if (empty($postText)) {
            $telegramChannelPostAdaptorDto->errors[] = 'Не указан текст поста. Обратитесь к инструкции.';
            return $result;
        }

        $result->text = implode("\n", $postText);
        // Проверяем корректность кода канала и его статуса
        if ($result->keyExist('codeChannel')) {
            $channelFilter = new ChannelFilter();
            $channelFilter->codes = [$result->codeChannel];
            $channel = $this->channelRepository->findByFilter($channelFilter)->first();
            $codes = $this->channelRepository->getPublishChannelCodes();
            if (!$channel) {
                $telegramChannelPostAdaptorDto->errors[] = 'Некорректный код канала, варианты: ' . implode(', ', $codes);
            } elseif ($channel->type_const !== ChannelType::TARGET) {
                $telegramChannelPostAdaptorDto->errors[] = 'Канал с кодом ' . $result->codeChannel . ' не предназначен для публикаций';
            } elseif (!in_array($channel->status_const, [ChannelStatus::ACTIVE, ChannelStatus::PAUSED])) {
                $telegramChannelPostAdaptorDto->errors[] = 'Канал ' . $result->codeChannel . ' должен быть активен или на паузе';
            } else {
                $result->targetChannelId = $channel->id;
            }
        }

        return $result;
    }
}

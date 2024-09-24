<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\TelegramCache;
use App\Services\CreatePostFromTelegramService;
use App\Telegram\Command\Base\Command;
use App\Telegram\Command\HelpCommand;
use App\Telegram\Command\StartCommand;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Update;

/**
 * Этот класс обрабатывает все входящие сообщения от Telegram.
 * Поддерживает как консольный режим (polling), так и webhook.
 * Локально рекомендуется использовать консольные команды для polling,
 * а на сервере — webhook для автоматической обработки.
 */
class TelegramHandler
{
    /**
     * Основная функция для обработки команд и сообщений Telegram.
     *
     * @param BotApi|Client $bot Экземпляр бота для отправки и получения сообщений
     */
    public function execute(BotApi|Client $bot)
    {
        // Регистрация команд, которые бот может обрабатывать
        $commands = [
            StartCommand::$name => StartCommand::class,
        ];

        // Обработка команд
        foreach ($commands as $name => $command) {
            $bot->command($name, function ($message) use ($bot, $command) {
                /** @var Command $command */
                $command = app($command, ['bot' => $bot, 'message' => $message]);
                app()->call([$command, 'hundler']);
                return true;
            });
        }

        // Обработка callback-запросов от Telegram (например, кнопки)
        $bot->callbackQuery(function (CallbackQuery $callbackQuery) use ($bot, $commands) {
            $id = $callbackQuery->getFrom()->getId();
            $data = $callbackQuery->getData();

            // Проверка, если callback содержит 'short::' (это может быть какой-то идентификатор)
            if (mb_strpos($data, 'short::') !== false) {
                $key = trim($data, 'short::');
                [$keyUserId, $uniqueCode] = explode(':', $key);
                $keyUserId = (int)$keyUserId;

                // Извлечение данных из кэша по ключу
                $data = TelegramCache::getValue($keyUserId, $key);

                if (is_array($data) && array_key_exists('classCommand', $data)) {
                    if ($data['classCommand'] === HelpInstructionCommand::class) {
                        // Обработка команды HelpInstructionCommand через callback
                        $command = app(
                            HelpInstructionCommand::class,
                            [
                                'bot' => $bot,
                                'message' => $callbackQuery->getMessage(),
                                'callbackQuery' => $callbackQuery,
                                'params' => ['callback' => $data]
                            ]
                        );
                        $command->setIsCallback();
                        $command->hundler();
                        return;
                    } else {
                        // Можно добавить обработку других команд или сообщений
                        // $this->replyWithMessage($id, 'Команда не найдена');
                    }
                }
            } else {
                // Неизвестный callback запрос
                // $this->replyWithMessage($id, 'Неизвестный запрос');
            }
        });

        // Обработка всех остальных сообщений (не команд)
        $bot->on(function (Update $update) use ($bot, $commands) {
            $message = $update->getMessage();
            $messageEdited = $update->getEditedMessage();

            $channelPostEdited = $update->getEditedChannelPost();
            $channelPost = $update->getChannelPost();

            // Если сообщение из канала было отредактировано, используем его вместо обычного
            if (!$channelPost && $channelPostEdited) {
                $channelPost = $channelPostEdited;
            }
            // Если сообщение из канала было отредактировано, используем его вместо обычного
            if (!$message && $messageEdited) {
                $message = $messageEdited;
            }
            // Обработка сообщений из каналов
            if ($channelPost) {
                $isMediaMessage = $channelPost->getPhoto() !== null || $channelPost->getVideo() !== null || $channelPost->getAudio() !== null;

                /** @var CreatePostFromTelegramService $createPostFromTelegramService */
                $createPostFromTelegramService = app(CreatePostFromTelegramService::class);

                // Создание поста из сообщения Telegram
                $result = $createPostFromTelegramService->execute($channelPost);

                // Если в результате есть ошибки, редактируем сообщение в Telegram
                if ($result->keyExist('errors')) {
                    if ($isMediaMessage) {
                        $bot->editMessageCaption(
                            $channelPost->getChat()->getId(),
                            $channelPost->getMessageId(),
                            $result->getTextForTelegram()
                        );
                    } else {
                        $bot->editMessageText(
                            $channelPost->getChat()->getId(),
                            $channelPost->getMessageId(),
                            $result->getTextForTelegram()
                        );
                    }
                }
                return;
            }

            // Обработка сообщений, которые могут приходить в группу или напрямую боту
            if ($message !== null) {

                // Если сообщение совпадает с командами Help
                if (in_array($message->getText(), HelpCommand::$names)) {
                    /** @var Command $command */
                    $command = app(HelpCommand::class, ['bot' => $bot, 'message' => $message]);
                    app()->call([$command, 'hundler']);
                    return;
                }
            }
        }, function () {
            // Всегда возвращаем true для продолжения обработки
            return true;
        });
    }
}

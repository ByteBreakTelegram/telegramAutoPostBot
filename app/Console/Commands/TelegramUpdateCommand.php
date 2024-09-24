<?php

namespace App\Console\Commands;

use App\Telegram\TelegramHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;
use Throwable;

/**
 * Команда для обработки обновлений от Telegram.
 * Используется с помощью: php artisan telegram:update
 */
class TelegramUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'telegram:update'; // Подпись команды

    /**
     * @var string
     */
    protected $description = 'Обработка команд которые приходят от телеграмма'; // Описание команды

    private Client $bot; // Экземпляр клиента Telegram

    /**
     * Выполнение команды
     */
    public function handle()
    {
        ini_set('memory_limit', '1G'); // Установка лимита памяти
        $this->bot = new Client(config('telegram.client.token')); // Инициализация клиента с токеном

        /** @var \App\Telegram\TelegramHandler $telegramHandler */
        $telegramHandler = app(TelegramHandler::class); // Получение экземпляра обработчика
        $telegramHandler->execute($this->bot); // Выполнение обработчика

        $this->info('Go'); // Информационное сообщение о запуске обработки

        $offset = 0; // Смещение для получения обновлений
        $limit = 100; // Максимальное количество обновлений для обработки
        $timeout = 0; // Таймаут для ожидания обновлений

        while (true) {
            /** @var Update[] $messages */
            $messages = $this->bot->getUpdates($offset, $limit, $timeout); // Получение обновлений

            // Обработка полученных сообщений
            $this->bot->handle($messages);
            try {
                // Блок для обработки ошибок
            } catch (Throwable $e) {
                // Логирование ошибок
                dump($e->getMessage(), $e->getTraceAsString());
            }

            /** @var Update|false $messageLast */
            $messageLast = end($messages); // Получаем последнее сообщение из обновлений

            // Удаляем обработанные сообщения
            if ($messageLast !== false) {
                $this->bot->getUpdates($messageLast->getUpdateId() + 1, $limit, $timeout);
            }

            usleep(999); // Пауза для предотвращения перегрузки
        }

        // Обработка исключений вне цикла
        try {
            // Блок для обработки ошибок
        } catch (Throwable $e) {
            // Логирование ошибок
            Log::error(
                'Throwable ' . $e->getMessage(),
                [
                    'extra' => [
                        'getTraceAsString' => $e->getTraceAsString(),
                    ],
                ]
            );
            $this->error($e->getMessage() . ': ' . $e->getTraceAsString());
        }
    }
}

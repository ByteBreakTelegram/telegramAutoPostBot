<?php

declare(strict_types=1);


namespace App\Telegram\Command\Base;


use App\Models\User;
use Illuminate\Support\Facades\DB;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Message;

abstract class Command
{
    protected bool $isCallback = false;

    public function __construct(
        protected readonly Client $bot,
        protected readonly Message $message,
        protected readonly CallbackQuery $callbackQuery,
        protected ?array $params = null
    ) {
    }

    public function hundler()
    {
        DB::transaction(function () {
            app()->call([$this, 'execute']);
        });
    }

    protected function replyWithMessage(string $message, $replyMarkup = null): void
    {
//        \TelegramBot\Api\BotApi::sendMessage();
        $this->bot->sendMessage(
            $this->message->getChat()->getId(),
            $message,
            $parseMode = 'markdown',
            $disablePreview = true,
            $replyToMessageId = null,
            $replyMarkup,
            $disableNotification = false,
            $messageThreadId = null,
            $protectContent = null,
            $allowSendingWithoutReply = null
        );
    }

    /**
     *
     *
     * $chat_id = 'CHAT_ID';
     * $title = 'Название товара';
     * $description = 'Описание товара';
     * $payload = 'Уникальный_идентификатор_платежа';
     * $provider_token = 'PROVIDER_TOKEN'; // Получите его от платежного провайдера, который поддерживает Telegram
     * $start_parameter = 'start';
     * $currency = 'USD';
     * $prices = [
     *     ['label' => 'Товар', 'amount' => 1000] // Цена в минимальных единицах валюты (например, 1000 = $10.00)
     * ];
     *
     *
     *
     * @param string $title
     * @param string $description
     * @param string|null $payload
     * @param string $providerToken
     * @param string $startParameter
     * @param string $currency
     * @param $prices
     * @return void
     */
    protected function sendInvoice(string $title, string $description, string $payload = null, string $providerToken, string $startParameter, string $currency, $prices ): void
    {
//        \TelegramBot\Api\BotApi::sendInvoice();
        $this->bot->sendInvoice(
            $this->message->getChat()->getId(),
            $title,
            $description,
            $payload,
            $providerToken,
            $startParameter,
            $currency,
            $prices,
            $isFlexible = false,
            $photoUrl = null,
            $photoSize = null,
            $photoWidth = null,
            $photoHeight = null,
            $needName = false,
            $needPhoneNumber = false,
            $needEmail = false,
            $needShippingAddress = false,
            $replyToMessageId = null,
            $replyMarkup = null,
            $disableNotification = false,
            $providerData = null,
            $sendPhoneNumberToProvider = false,
            $sendEmailToProvider = false,
            $messageThreadId = null,
            $protectContent = null,
            $allowSendingWithoutReply = null
        );
    }

    protected function editMessageText(string $message, $replyMarkup = null)
    {
        $this->bot->editMessageText(
            chatId:         $this->message->getChat()->getId(),
            messageId:      $this->message->getMessageId(),
            text:           $message,
            replyMarkup:    $replyMarkup,
            parseMode:      'markdown',
            disablePreview: true,
        );
    }

    /**
     * Если сообщение от бота, редактируем его
     * @param string $message
     * @param $replyMarkup
     * @return void
     */
    protected function editOrReplyMessageText(string $message, $replyMarkup = null)
    {
        if ($this->message->getFrom()->isBot()) {
            $this->editMessageText($message, $replyMarkup);
        } else {
            $this->replyWithMessage($message, $replyMarkup);
        }
    }

    /**
     * Ответить молча, чтоб пропали часики на кнопке $this->answerCallbackQuery(null, false);
     * @param string|null $text - текст сообщения, можно оставить пустым
     * @param bool $showAlert - true, показать alert как в браузере
     * @param string|null $url
     * @param int $cacheTime
     * @return void
     */
    protected function answerCallbackQuery(
        string $text = null,
        bool $showAlert = false,
        string $url = null,
        int $cacheTime = 0
    ) {
        $this->bot->answerCallbackQuery(
            callbackQueryId: $this->callbackQuery->getId(),
            text:            $text,
            showAlert:       $showAlert,
            url:             $url,
            cacheTime:       $cacheTime,
        );
    }


    protected function deleteMessage()
    {
        $this->bot->deleteMessage($this->message->getChat()->getId(), $this->message->getMessageId());
    }

    protected function getUser(): User
    {
        return User::query()->where('telegram_chat_id', $this->message->getFrom()->getId())->first();
    }


    public function setIsCallback()
    {
        $this->isCallback = true;
    }

}
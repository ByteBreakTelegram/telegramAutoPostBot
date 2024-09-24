<?php

declare(strict_types=1);

namespace App\Telegram\Command;

use App\Actions\Dto\UserCreateActionDto;
use App\Actions\UserCreateAction;
use App\Helpers\LanguageEnumHelper;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Telegram\Command\Base\Command;
use App\Telegram\Menu\TelegramMainMenu;

/**
 * Обработчик команды "start".
 * При нажатии на старт, создается пользователь и отображается главное меню.
 */
final class StartCommand extends Command
{
    public static string $name = 'start'; // Имя команды

    protected string $description = 'Создание пользователя и главное меню'; // Описание команды

    public function execute(UserCreateAction $userCreateAction)
    {
        // Поиск пользователя по его Telegram ID
        /** @var User|null $user */
        $user = User::query()->where('telegram_chat_id', $this->message->getFrom()->getId())->first();
        $isNew = false; // Флаг для проверки, новый ли пользователь

        if (!$user) {
            // Если пользователь не найден, создаем нового
            $referrer = null; // Идентификатор реферера (если есть)
            $isNew = true; // Пользователь новый
            $userCreateDto = new UserCreateActionDto(); // Создаем DTO для пользователя
            $userCreateDto->name = $this->message->getFrom()->getFirstName(); // Имя пользователя
            $userCreateDto->telegram_chat_id = $this->message->getFrom()->getId(); // Telegram ID
            $telegram_username = $this->message->getFrom()->getUsername(); // Имя пользователя в Telegram

            // Если имя пользователя не указано, используем ID
            if ($telegram_username === null) {
                $telegram_username = 'id_' . $this->message->getFrom()->getId();
            }
            $userCreateDto->telegram_username = $telegram_username; // Устанавливаем имя пользователя

            // Устанавливаем язык, проверяем наличие в перечислении языков
            if (!in_array($this->message->getFrom()->getLanguageCode(), LanguageEnumHelper::scalar())) {
                $userCreateDto->language_code = LanguageEnumHelper::from($this->message->getFrom()->getLanguageCode());
            } else {
                $userCreateDto->language_code = LanguageEnumHelper::RU; // По умолчанию русский
            }

            // Устанавливаем дополнительные параметры пользователя
            $userCreateDto->lname = null; // Фамилия (пока не используется)
            $userCreateDto->parent_id = $referrer; // Идентификатор реферера
            $userCreateDto->role_const = UserRole::USER; // Роль пользователя
            $userCreateDto->is_premium = $this->message->getFrom()->getIsPremium() ?? false; // Проверяем статус премиум
            $userCreateDto->is_bot = $this->message->getFrom()->isBot() ?? false; // Проверяем, является ли пользователь ботом

            // Создаем пользователя
            $user = $userCreateAction->execute($userCreateDto);
        }

        // Формируем текст сообщения в зависимости от того, новый пользователь или нет
        if ($isNew) {
            $text = 'Поздравляем с регистрацией';
        } else {
            $text = 'С чего начнем?';
        }

        // Отправляем ответ с текстом и главным меню
        $this->replyWithMessage(
            $text,
            TelegramMainMenu::main($user) // Получаем главное меню для пользователя
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Telegram\Command;

use App\Models\User;
use App\Telegram\Command\Base\Command;
use App\Telegram\Menu\TelegramHelpMenu;

/**
 * Справочная информация
 */
final class HelpCommand extends Command
{
    public static array $names = ['❓ Помощь'];

    private User $user;

    public function execute(): void
    {
        $this->user = User::findByTelegramId($this->message->getChat()->getId());

        $text = "
❓ **Основная механика бота**
👉 Добавляем бота в канал с истоником постов
👉 Добавляем бота в каналы куда публиковать посты
👉 В канал с источником постов добаляем посты и ставим их в очередь на публикацию 
👉 Посты автоматически публикуются
        ";

        $this->replyWithMessage($text, TelegramHelpMenu::main($this->user));
    }
}
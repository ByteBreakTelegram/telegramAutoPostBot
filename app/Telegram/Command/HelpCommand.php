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
❓ **Как создать пост**
        ";

        $this->replyWithMessage($text, TelegramHelpMenu::main($this->user));
    }
}
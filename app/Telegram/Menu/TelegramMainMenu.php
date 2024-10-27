<?php

declare(strict_types=1);

namespace App\Telegram\Menu;

use App\Models\User;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramMainMenu
{

    /**
     * @param User $user
     * @param array $linesMenu
     * @return ReplyKeyboardMarkup
     */
    public static function main(User $user, array $linesMenu = []): ReplyKeyboardMarkup
    {

        $btmInline = [
            [
                [
                    'text' => trans('❓ Помощь'),
                ],
            ],
            [
                [
                    'text' => trans('Настройка каналов'),
                ],
            ],
        ];
        foreach ($linesMenu as $lineMenu) {
            $btmInline[] = $lineMenu;
        }
        return new ReplyKeyboardMarkup($btmInline, null, true);
    }
}
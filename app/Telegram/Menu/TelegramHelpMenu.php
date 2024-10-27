<?php

declare(strict_types=1);

namespace App\Telegram\Menu;

use App\Models\TelegramCache;
use App\Models\User;
use App\Telegram\Command\HelpCommand;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramHelpMenu
{

    /**
     * @param User $user
     * @param array $linesMenu
     * @return InlineKeyboardMarkup
     */
    public static function main(User $user, array $linesMenu = []): InlineKeyboardMarkup
    {
        $btmInline = [
//            [
//                [
//                    'text' => 'Инструкция',
//                    'callback_data' => 'short::' . TelegramCache::setValue(
//                            $user->id,
//                            uniqid($user->id . ':'),
//                            [
//                                'classCommand' => HelpCommand::class,
//                            ],
//                        )
//                ],
//                [
//                    'text' => 'Популярные вопросы',
//                    'web_app' => ['url' => 'https://en.com/faq/']
//                ],
//            ],
//            [
//                [
//                    'text' => 'Техподдрежка',
//                    'url' => 'https://t.me/dfdf',
//                ]
//            ],
        ];

        foreach ($linesMenu as $lineMenu) {
            $btmInline[] = $lineMenu;
        }

        return new InlineKeyboardMarkup($btmInline);
    }
}
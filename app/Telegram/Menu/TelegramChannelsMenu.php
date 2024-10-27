<?php

declare(strict_types=1);

namespace App\Telegram\Menu;

use App\Models\Channel;
use App\Models\TelegramCache;
use App\Models\User;
use App\Services\Dto\ChannelSettingDto;
use App\Telegram\Command\ChannelSettingCommand;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Меню каналов
 */
class TelegramChannelsMenu
{

    /**
     * @param User $user
     * @param array $linesMenu
     * @return InlineKeyboardMarkup
     */
    public static function main(User $user, array $linesMenu = []): InlineKeyboardMarkup
    {
        $result = [];
        $channels = Channel::query()
            ->orderByDesc('id')
            ->get();
        $temp = [];
        /** @var Channel $channel */
        foreach ($channels as $channel) {
            $temp[] = [
                'text' => $channel->title,
                'callback_data' => 'short::' . TelegramCache::setValue(
                        $user->id,
                        uniqid($user->id. ':'),
                        [
                            'classCommand' => ChannelSettingCommand::class,
                            'dto' => [
                                'className' => ChannelSettingDto::class,
                                'data' => [
                                    'channelId' => $channel->id,
                                ]
                            ],
                        ],
                    )
            ];
            if (count($temp) === 3) {
                $result[] = $temp;
                $temp = [];
            }
        }
        if ($temp !== []) {
            $result[] = $temp;
        }
        foreach ($linesMenu as $lineMenu) {
            $result[] = $lineMenu;
        }

        return new InlineKeyboardMarkup($result);
    }
}
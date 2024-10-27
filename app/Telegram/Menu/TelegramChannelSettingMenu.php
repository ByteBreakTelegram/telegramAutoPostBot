<?php

declare(strict_types=1);

namespace App\Telegram\Menu;

use App\Models\Channel;
use App\Models\Enums\ChannelPostInterval;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use App\Models\TelegramCache;
use App\Models\User;
use App\Services\Dto\ChannelSettingDto;
use App\Telegram\Command\ChannelSettingCommand;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

/**
 * Меню настройки канала
 */
class TelegramChannelSettingMenu
{

    /**
     * @param User $user
     * @param Channel $channel
     * @param array $linesMenu
     * @return InlineKeyboardMarkup
     */
    public static function main(User $user, Channel $channel, array $linesMenu = []): InlineKeyboardMarkup
    {
        $btnType[] = [
            'text' => ($channel->type_const === ChannelType::TARGET ? '✅ ' : '') . 'Канал публикаций',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'type_const' => ChannelType::TARGET,
                            ]
                        ],
                    ],
                )
        ];
        $btnType[] = [
            'text' => ($channel->type_const === ChannelType::SOURCE ? '✅ ' : '') . 'Канал истоник',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'type_const' => ChannelType::SOURCE,
                            ]
                        ],
                    ],
                )
        ];
        $btnSource[] = [
            'text' => ($channel->status_const === ChannelStatus::ACTIVE ? '✅ ' : '') . 'Активен',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'status_const' => ChannelStatus::ACTIVE,
                            ]
                        ],
                    ],
                )
        ];
        $btnSource[] = [
            'text' => ($channel->status_const === ChannelStatus::PAUSED ? '✅ ' : '') . 'На паузе',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'status_const' => ChannelStatus::PAUSED,
                            ]
                        ],
                    ],
                )
        ];
        $btnSource[] = [
            'text' => ($channel->status_const === ChannelStatus::DELETED ? '✅ ' : '') . 'Удален',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'status_const' => ChannelStatus::DELETED,
                            ]
                        ],
                    ],
                )
        ];
        $btnIsBusinessHours = [];


        $settingTarget = [];
        if ($channel->type_const === ChannelType::TARGET) {
            $settingTarget = [
                [
                    'text' => 'Настроить время публиаций ➡️',
                    'callback_data' => 'short::' . TelegramCache::setValue(
                            $user->id,
                            uniqid($user->id . ':'),
                            [
                                'classCommand' => ChannelSettingCommand::class,
                                'dto' => [
                                    'className' => ChannelSettingDto::class,
                                    'data' => [
                                        'channelId' => $channel->id,
                                        'isSettingTimePublic' => true
                                    ],
                                ],
                            ],
                        )
                ],
            ];
            $btnIsBusinessHours[] = [
                'text' => ($channel->is_business_hours  ? '✅ ' : '❌ ') . 'Публиковать в рабочие часы',
                'callback_data' => 'short::' . TelegramCache::setValue(
                        $user->id,
                        uniqid($user->id . ':'),
                        [
                            'classCommand' => ChannelSettingCommand::class,
                            'dto' => [
                                'className' => ChannelSettingDto::class,
                                'data' => [
                                    'channelId' => $channel->id,
                                    'is_business_hours' => true,
                                ]
                            ],
                        ],
                    )
            ];
        }


        $btmInline = [
            $btnType,
            $btnSource,
            $settingTarget,
            $btnIsBusinessHours,
            [
                [
                    'text' => '↩️ Назад',
                    'callback_data' => 'short::' . TelegramCache::setValue(
                            $user->id,
                            uniqid($user->id . ':'),
                            [
                                'classCommand' => ChannelSettingCommand::class,
                            ],
                        )
                ],
            ],
        ];

        foreach ($linesMenu as $lineMenu) {
            $btmInline[] = $lineMenu;
        }

        return new InlineKeyboardMarkup($btmInline);
    }

    /**
     * @param User $user
     * @param Channel $channel
     * @param array $linesMenu
     * @return InlineKeyboardMarkup
     */
    public static function time(User $user, Channel $channel, array $linesMenu = []): InlineKeyboardMarkup
    {
        $btnType[] = [
            'text' => ($channel->post_interval_const === ChannelPostInterval::SIX_HOURS ? '✅ ' : '') . 'Каждые 6 часов',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'post_interval_const' => ChannelPostInterval::SIX_HOURS,
                            ]
                        ],
                    ],
                )
        ];
        $btnType[] = [
            'text' => ($channel->post_interval_const === ChannelPostInterval::TWELVE_HOURS ? '✅ ' : '') . 'Каждые 12 часов',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'post_interval_const' => ChannelPostInterval::TWELVE_HOURS,
                            ]
                        ],
                    ],
                )
        ];
        $btnType2[] = [
            'text' => ($channel->post_interval_const === ChannelPostInterval::ONE_DAY ? '✅ ' : '') . 'Раз в сутки',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'post_interval_const' => ChannelPostInterval::ONE_DAY,
                            ]
                        ],
                    ],
                )
        ];
        $btnType2[] = [
            'text' => ($channel->post_interval_const === ChannelPostInterval::THREE_DAYS ? '✅ ' : '') . 'Раз в три дня',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'post_interval_const' => ChannelPostInterval::THREE_DAYS,
                            ]
                        ],
                    ],
                )
        ];
        $btnType2[] = [
            'text' => ($channel->post_interval_const === ChannelPostInterval::SEVEN_DAYS ? '✅ ' : '') . 'Раз в неделю',
            'callback_data' => 'short::' . TelegramCache::setValue(
                    $user->id,
                    uniqid($user->id . ':'),
                    [
                        'classCommand' => ChannelSettingCommand::class,
                        'dto' => [
                            'className' => ChannelSettingDto::class,
                            'data' => [
                                'channelId' => $channel->id,
                                'post_interval_const' => ChannelPostInterval::SEVEN_DAYS,
                            ]
                        ],
                    ],
                )
        ];


        $btmInline = [
            $btnType,
            $btnType2,
            [
                [
                    'text' => '↩️ Назад',
                    'callback_data' => 'short::' . TelegramCache::setValue(
                            $user->id,
                            uniqid($user->id . ':'),
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
                ],
            ],
        ];

        foreach ($linesMenu as $lineMenu) {
            $btmInline[] = $lineMenu;
        }

        return new InlineKeyboardMarkup($btmInline);
    }
}
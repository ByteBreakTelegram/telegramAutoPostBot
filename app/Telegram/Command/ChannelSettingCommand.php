<?php

declare(strict_types=1);

namespace App\Telegram\Command;

use App\Models\Channel;
use App\Models\Enums\ChannelPostInterval;
use App\Models\Enums\ChannelStatus;
use App\Models\Enums\ChannelType;
use App\Models\User;
use App\Services\ChannelModelService;
use App\Services\Dto\ChannelModelDto;
use App\Services\Dto\ChannelSettingDto;
use App\Telegram\Command\Base\Command;
use App\Telegram\Menu\TelegramChannelSettingMenu;
use App\Telegram\Menu\TelegramChannelsMenu;

/**
 * Настройка каналов
 */
final class ChannelSettingCommand extends Command
{
    public static array $names = ['userChannelThread', 'Настройка каналов'];
    private ?User $user;


    public function execute(
        ChannelModelService $channelModelService,
    ): void
    {
        $userTelegramId = $this->message->getChat()->getId();
        $this->user = User::findByTelegramId($userTelegramId);
        if (!$this->user) {
            $this->replyWithMessage('Ошибка');
        }
        $dto = null;
        if ($this->params !== null && key_exists('callback', $this->params) && key_exists('dto', $this->params['callback']) && $this->params['callback']['dto']['className'] === ChannelSettingDto::class) {
            $dto = ChannelSettingDto::createFromArray($this->params['callback']['dto']['data']);
        }
        // выбрали конкретный канал
        if ($dto) {
            $channel = null;
            if ($dto->keyExist('channelId')) {
                /** @var Channel $channel */
                $channel = Channel::query()->find($dto->channelId);
            }
            if ($channel) {
                if ($dto->keyExist('status_const')) {
                    $channelModelDto = new ChannelModelDto();
                    $channelModelDto->status_const = ChannelStatus::from($dto->status_const);
                    $channelModelService->update($channel, $channelModelDto);
                } elseif ($dto->keyExist('type_const')) {
                    $channelModelDto = new ChannelModelDto();
                    $channelModelDto->type_const = ChannelType::from($dto->type_const);
                    $channelModelService->update($channel, $channelModelDto);
                } elseif ($dto->keyExist('isSettingTimePublic') || $dto->keyExist('post_interval_const') || $dto->keyExist('is_business_hours')) {
                    if ($dto->keyExist('post_interval_const')) {
                        $channelModelDto = new ChannelModelDto();
                        $channelModelDto->post_interval_const = ChannelPostInterval::from($dto->post_interval_const);
                        $channelModelService->update($channel, $channelModelDto);
                    }
                    if ($dto->keyExist('isSettingTimePublic') || $dto->keyExist('post_interval_const')) {

                        $text = "
Настройки канала *{$channel->title}*:

Изменить время публикаций:
";
                        $this->editOrReplyMessageText($text, TelegramChannelSettingMenu::time($this->user, $channel));
                        return;
                    }


                    if ($dto->keyExist('is_business_hours')) {
                        $channelModelDto = new ChannelModelDto();
                        $channelModelDto->is_business_hours = !$channel->is_business_hours;
                        $channelModelService->update($channel, $channelModelDto);
                    }


                }
                    $text = "
Настройки канала *{$channel->title}*:

Изменить настройки:
";
                $this->editOrReplyMessageText($text, TelegramChannelSettingMenu::main($this->user, $channel));
            }
        } else {
            $text = "
            Выбирете канал:
        ";
            $this->editOrReplyMessageText($text, TelegramChannelsMenu::main($this->user));
        }
    }
}
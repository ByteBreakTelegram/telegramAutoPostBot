<?php

declare(strict_types=1);


namespace App\Actions;


use App\Actions\Dto\UserCreateActionDto;
use App\Models\User;
use App\Services\UserModelService;

/**
 * Создание пользователя
 */
class UserCreateAction
{

    public function __construct(
        protected readonly UserModelService $userModelService,
    )
    {
    }

    public function execute(UserCreateActionDto $userCreateActionDto): User
    {
        $user = new User();

        $user->telegram_chat_id = $userCreateActionDto->telegram_chat_id;

        $user->name = $userCreateActionDto->name;
        $user->telegram_username = $userCreateActionDto->telegram_username;
        $user->language_code = $userCreateActionDto->language_code;
        $user->lname = $userCreateActionDto->lname;
        $user->is_premium = $userCreateActionDto->is_premium;
        $user->is_bot = $userCreateActionDto->is_bot;
        $user->role_const = $userCreateActionDto->role_const;
        $user->parent_id = $userCreateActionDto->parent_id;

        $user->save();

        return $user;
    }
}
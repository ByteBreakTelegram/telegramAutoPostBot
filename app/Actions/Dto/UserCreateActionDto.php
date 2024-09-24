<?php

declare(strict_types=1);


namespace App\Actions\Dto;


use App\Components\Dto\Dto;
use App\Helpers\LanguageEnumHelper;
use App\Models\Enums\UserRole;

class UserCreateActionDto extends Dto
{
    public string $name;
    public int $telegram_chat_id;
    public string $telegram_username;
    public LanguageEnumHelper $language_code;
    public ?string $lname;
    public UserRole $role_const;
    public bool $is_premium;
    public bool $is_bot;
    public ?int $parent_id;
}
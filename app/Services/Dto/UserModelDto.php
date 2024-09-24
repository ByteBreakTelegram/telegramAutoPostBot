<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;
use App\Helpers\LanguageEnumHelper;

class UserModelDto extends Dto
{
    public LanguageEnumHelper $language_code;
}
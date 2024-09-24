<?php

declare(strict_types=1);


namespace App\Services;


use App\Models\User;
use App\Services\Dto\UserModelDto;
use App\Validators\UserModelValidator;

class UserModelService
{
    public function __construct(protected readonly UserModelValidator $validator)
    {
    }


    public function update(User $user, UserModelDto $dto)
    {
        $this->validator->validateDto($dto, UserModelValidator::SCENARIO_UPDATE);
        $user->fill(
            $dto->toArray(
                [
                    'language_code',
                ]
            )
        );
        $user->save();
        return $user;
    }
}
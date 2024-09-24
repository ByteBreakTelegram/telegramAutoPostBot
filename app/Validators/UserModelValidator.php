<?php

declare(strict_types=1);


namespace App\Validators;


use App\Components\Dto\Dto;
use App\Helpers\LanguageEnumHelper;
use App\Models\User;
use App\Rules\EnumRule;
use App\Validators\Core\ServiceModelValidator;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;

class UserModelValidator extends ServiceModelValidator
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return match ($this->getScenario()) {
            self::SCENARIO_CREATE => [
                'language_code' => ['nullable', new EnumRule(LanguageEnumHelper::class)],
            ],
            self::SCENARIO_UPDATE => [
                'language_code' => ['required', new EnumRule(LanguageEnumHelper::class)],
            ],

            default => throw new \Exception('UserModelValidator. Нет указанного константа сценария ' . $this->getScenario()),
        };
    }

    /**
     * @param Dto $dto
     * @param string $scenario
     * @return void
     * @throws BindingResolutionException
     * @throws ValidationException
     * @throws Exception
     */
    public function validateDto(Dto $dto, string $scenario): void
    {
        $this->setScenario($scenario);
        $this->setDataFromDto($dto);
        $this->dtoCheckCountField($dto);
        $this->validate();
        $this->fillDto($dto);
    }

    public function getModelClass(): string
    {
        return User::class;
    }

}
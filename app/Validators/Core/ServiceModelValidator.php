<?php

declare(strict_types=1);


namespace App\Validators\Core;



use App\Components\Dto\Dto;

abstract class ServiceModelValidator extends ServiceValidator
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';


    /**
     * Класс модели валидации
     * @return string
     */
    abstract public function getModelClass(): string;


    /**
     * Проверка наличия атрибута фильтрации в правилах
     * Пример
     * public function validateDto(DtoAbstract &$dto): void
     * {
     *     $this->dtoCheckCountField($dto);
     *     $this->setData($dto->toArray());
     *     if ($dto instanceof CandidateCreateDto) {
     *         $this->baseCreatedValidate();
     *     }
     *     parent::validateDto($dto);
     * }
     *
     * @param Dto $dto
     * @param string $scenario
     * @throws \Exception
     */
    abstract public function validateDto(Dto $dto, string $scenario): void;


}

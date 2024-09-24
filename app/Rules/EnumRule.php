<?php

declare(strict_types=1);

namespace App\Rules;

use TypeError;
use UnitEnum;


/**
 * Проверка enum типов, можно передать как скалярное значение, так и сам enum значение
 */
class EnumRule extends Rule
{
    protected ?string $errorCode = 'validateIn';

    public function __construct(
        protected readonly string $className
    ) {
    }

    protected function check(UnitEnum|null|string|int $value, string $attribute): bool
    {
        if ($value instanceof UnitEnum) {
            return get_class($value) === $this->className;
        } else {
            try {
                return !is_null($this->className::tryFrom($value));
            } catch (TypeError $e) {
                return false;
            }
        }
    }

}

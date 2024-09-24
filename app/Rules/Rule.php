<?php

declare(strict_types=1);


namespace App\Rules;


use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class Rule implements ValidationRule
{
    /**
     * Код ошибки после выполнения валидации
     * @var ?string
     */
    protected ?string $errorCode = null;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    final public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->check($value, $attribute) ) {
            $fail($this->errorCode);
        }
    }
}

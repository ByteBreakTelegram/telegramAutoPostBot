<?php

declare(strict_types=1);

namespace App\Validators\Core;


use Exception;

class ValidationServiceException extends Exception
{
    protected string $literalCode;
    protected string $field;

    public function __construct(string $literalCode, string $field, ?string $message)
    {
        $this->literalCode = $literalCode;
        $this->field = $field;
        parent::__construct($message ?? trans('literal.' . $literalCode), 422);
    }


    public function getLiteralCode(): ?string
    {
        return $this->literalCode;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}

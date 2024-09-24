<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Console\Commands\Translation\LiteralCommand;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class ValidatorHelper
{
    /**
     * Генератор кодов ошибок
     * @param array $fieldsRules
     * @return array
     * @throws \Exception
     */
    public static function generateDefaultMessage(array $fieldsRules): array
    {
        $result = [];
        $allow = [
            'integer',
            'string',
            'required',
            'max',
            'min',
            'size',
            'boolean',
            'url',
            'array',
            'numeric',
            'regex',
            'in',
            'not_in',
            'gt',
            'gte',
            'lt',
            'lte',
            'required_if',
            'required_with',
            'required_without',
            'uuid',
            'date',
            'mimes',
            'email',
            'phone',
            'enum',
            'between',
            'digits_between',
            'digits',
            'file',
            'email:dns',
            'distinct',
            'prohibited',
            'ip',
            'alpha_num',
            'ipv4',
        ];

        $notAllow = ['unique'];
        foreach ($fieldsRules as $field => $rule) {
            if (!is_array($rule)) {
                $rule = explode('|', $rule);
            }
            foreach ($rule as $itemRule) {
                if (is_object($itemRule)) {
                    if ($itemRule instanceof Enum) {
                        $itemRule = 'enum';
                    } else {
                        $itemRule = (string) $itemRule;
                    }
                }
                if (
                    str_contains($itemRule, 'required_if:') !== false ||
                    str_contains($itemRule, 'required_with:') !== false ||
                    str_contains($itemRule, 'required_without:') !== false
                ) {
                    $itemRule = 'required';
                }
                $itemRules = [];
                if (str_contains($itemRule, 'regex:') !== false ||
                    str_contains($itemRule, 'array:') !== false ||
                    str_contains($itemRule, 'in:') !== false ||
                    str_contains($itemRule, 'not_in:') !== false ||
                    str_contains($itemRule, 'max:') !== false ||
                    str_contains($itemRule, 'min:') !== false ||
                    str_contains($itemRule, 'gt:') !== false ||
                    str_contains($itemRule, 'gte:') !== false ||
                    str_contains($itemRule, 'lt:') !== false ||
                    str_contains($itemRule, 'lte:') !== false ||
                    str_contains($itemRule, 'unique:') !== false ||
                    str_contains($itemRule, 'exists:') !== false ||
                    str_contains($itemRule, 'mimes:') !== false ||
                    str_contains($itemRule, 'phone:') !== false ||
                    str_contains($itemRule, 'between:') !== false ||
                    str_contains($itemRule, 'digits_between:') !== false ||
                    str_contains($itemRule, 'alpha_num:') !== false ||
                    str_contains($itemRule, 'digits:') !== false ||
                    str_contains($itemRule, 'distinct:') !== false ||
                    str_contains($itemRule, 'size:') !== false
                ) {
                    $itemRules = explode(':', $itemRule);
                    $itemRule = $itemRules[0];
                }

                if (in_array($itemRule, $notAllow)) {
                    throw new \Exception('Валидатор запрещен, используй создание отдельного метода для валидации: ' . $itemRule);
                }

                if (!in_array($itemRule, $allow)) {
                    continue;
                }

                if (str_contains($itemRule, ':') !== false) {
                    $itemRule = str_replace(':', '', $itemRule);
                }
                $codeLiteral = 'validate';
                if ($itemRule == 'emaildns') {
                    $codeLiteral .= 'Email';
                    $itemRule = 'email';
                } elseif (in_array($itemRule, ['min', 'max', 'gt', 'gte', 'lt', 'lte', 'digits', 'size']) && array_key_exists(1, $itemRules)) {
                    if (in_array('integer', $rule)) {
                        $codeLiteral .= 'Integer';
                    } elseif (in_array('numeric', $rule)) {
                        $codeLiteral .= 'Numeric';
                    } elseif (in_array('array', $rule)) {
                        $codeLiteral .= 'Array';
                    } elseif (in_array('file', $rule)) {
                        $codeLiteral .= 'File';
                    } else {
                        $codeLiteral .= 'String';
                    }
                    $codeLiteral .= ucfirst($itemRule);
                    $codeLiteral .= $itemRules[1];
                } elseif ($itemRule === 'not_in') {
                    $codeLiteral .= ucfirst(Str::camel($itemRule));
                } elseif (in_array($itemRule, ['between', 'digits_between'])) {
                    $codeLiteral .= ucfirst(Str::camel($itemRule));
                    $codeLiteral .= $itemRules[1] ?? '';
                } elseif (in_array($itemRule, ['alpha_num'])) {
                    $codeLiteral .= ucfirst(Str::camel($itemRule));
                    $codeLiteral .= ucfirst($itemRules[1] ?? '');
                } elseif ($itemRule === 'distinct') {
                    $codeLiteral .= 'Unique';
                } else {
                    $codeLiteral .= ucfirst($itemRule);
                }
                $result[$field . '.' . $itemRule] = $codeLiteral;
            }
        }
        return $result;
    }

}
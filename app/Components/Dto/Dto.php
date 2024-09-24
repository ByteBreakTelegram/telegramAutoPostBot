<?php

declare(strict_types=1);


namespace App\Components\Dto;


use Exception;
use Illuminate\Support\Arr;

class Dto
{

    /**
     * Проверяет, установлено ли свойство
     * @param string $field
     * @return bool
     * @throws Exception
     */
    public function keyExist(string $field): bool
    {
        if (!array_key_exists($field, get_object_vars($this))) {
            if (!property_exists($this, $field)) {
                throw new Exception("{" . get_class($this) . "} не содержит свойства: " . $field);
            }
            return false;
        }
        return true;
    }

    public static function createFromArray(array $data): static
    {
        $result = new static();
        foreach ($data as $key => $datum) {
            $result->{$key} = $datum;
        }
        return $result;
    }

    /**
     * @param array|null $fields
     * @return array
     */
    public function toArray(?array $fields = null): array
    {
        $data = (array)$this;
        foreach ($data as $key => $value) {
            if ($value instanceof Dto) {
                $data[$key] = $value->toArray();
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($v instanceof Dto) {
                        $data[$key][$k] = $v->toArray();
                    }
                }
            }
        }
        if (!is_null($fields)) {
            return Arr::only($data, $fields);
        }
        return $data;
    }
}
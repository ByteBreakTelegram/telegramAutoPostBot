<?php

declare(strict_types=1);

namespace App\Validators\Core;

use App;
use App\Components\Dto\Dto;
use Arr;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Log;

class ServiceValidator
{
    public const SCENARIO_DEFAULT = 'default';

    /**
     * @var string Сценарий валидации
     */
    private string $scenario;

    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'default';

    /**
     * The validator instance.
     *
     * @var Validator|null
     */
    protected $validator;

    /**
     * @var array
     */
    protected $data;

    private array $fieldRequired = [];

    public function rules(): array
    {
        return [];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return Validator
     * @throws BindingResolutionException
     */
    protected function getValidatorInstance()
    {
        $validator = \Validator::make(
            $this->validationData(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
        $this->validator = $validator;

        return $this->validator;
    }

    /**
     * Получить валидные данные
     *
     * @return array
     */
    public function validationData()
    {
        return $this->data;
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     * @throws ValidationException
     * @throws BindingResolutionException
     */
    public function validate()
    {
        $this->getValidatorInstance();
        $this->validator->setRules($this->rules()); // @phpstan-ignore-line
        $this->validator->setData($this->data); // @phpstan-ignore-line
        $this->after($this->validator); // @phpstan-ignore-line
        return $this->validator->validate();
    }

    /**
     * Дополнительные правила проверки
     * public function after(\Illuminate\Validation\Validator $validator): void
     * {
     *     $validator->after(function($validator) {
     *         if (!$this->exists()) {
     *             $validator->errors()->add('field', $rule->message());
     *         }
     *     });
     * }
     * @param \Illuminate\Validation\Validator $validator
     */
    public function after(\Illuminate\Validation\Validator $validator): void
    {
    }

    /**
     * Get custom messages for validator errors.
     *
     * public function messages(): array
     * {
     *     $message = $this->generateDefaultMessage();
     *     return array_merge($message, []);
     * }
     *
     * @return array
     */
    public function messages()
    {
        return $this->generateDefaultMessage();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param bool $checkEmpty если передать true, то keyExist отдает false если в поле нет значения
     * @return bool
     */
    public function keyExist(string $key, bool $checkEmpty = false): bool
    {
        if (Arr::has($this->data, $key)) {
            // Проверки null и пустых строк не делаем
            if ($checkEmpty === false) {
                return true;
            }

            $value = data_get($this->data, $key);

            if ($value === null || $value === '') {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get(string $key): mixed
    {
        if (!$this->keyExist($key)) {
            throw new Exception('Undefined array key ' . $key);
        }
        return data_get($this->data, $key);
    }

    /**
     * @param string $key
     * @param mixed $val
     */
    public function set(string $key, mixed $val): void
    {
        $this->data[$key] = $val;
    }

    /**
     * Преобразует атрибуты из правил в массив, с учетом массивов
     * @return array
     */
    protected function getAttributesInRule(): array
    {
        $attributes = array_keys($this->rules());
        $return = [];
        foreach ($attributes as $attribute) {
            if (mb_strpos($attribute, '.') !== false) {
                $temp = explode('.', $attribute);
                $count = count($temp);
                if ($count > 1) {
                    $return = $this->getAttributesInRuleItem($return, $temp, 0, $count);
                }
            } else {
                $return[$attribute] = [];
            }
        }
        return $return;
    }

    /**
     * @param array $element
     * @param array $temp
     * @param int $key
     * @param int $count
     * @return array
     */
    protected function getAttributesInRuleItem(array $element, array $temp, int $key, int $count): array
    {
        if (!array_key_exists($temp[$key], $element)) {
            $element[$temp[$key]] = [];
        }
        if ($count > ($key + 1)) {
            $element[$temp[$key]] = $this->getAttributesInRuleItem($element[$temp[$key]], $temp, ++$key, $count);
        }
        return $element;
    }


    /**
     * Автоматическая генерация кодов литералов
     * @return array
     * @throws Exception
     */
    protected function generateDefaultMessage()
    {
        return [];
    }

    protected function setDataFromDto(Dto $dto): void
    {
        $this->setData($dto->toArray());
    }

    public function getScenario(): ?string
    {
        return $this->scenario;
    }

    public function setScenario(string $scenario): void
    {
        $this->scenario = $scenario;
    }


    /**
     * Заполнение DTO данными
     * @param Dto $dto
     * @param array|null $data
     */
    public function fillDto(Dto $dto, ?array $data = null): void
    {
        if (is_null($data)) {
            $data = $this->data;
        }

        $this->prepareValuesRecursive($dto, $data);
    }


    /**
     * Првоерка что все поля в DTO проверяется
     * @param object $dto
     * @throws Exception
     */
    protected function dtoCheckCountField(object $dto): void
    {
        $attributes = array_keys($dto->toArray());
        $rules = array_keys($this->rules());
        $keyNotValidate = array_diff($attributes, $rules);
        if (count($keyNotValidate) > 0) {
            Log::error(
                'DtoValidate. Не все атрибуты валидируются',
                [
                    'extra' => [
                        'attributesNotRules' => $keyNotValidate,
                        'dto' => get_class($dto),
                        'dtoData' => $dto->toArray(),
                        'rulesAttr' => array_keys($this->rules()),
                    ],
                ]
            );
            throw new Exception('dtoCheckCountField. Не все атрибуты валидируются: ' . implode(', ', $keyNotValidate));
        }

        $attributesInRule = $this->getAttributesInRule();

        // проверка вложенных массивов
        foreach ($dto->toArray() as $attribute => $values) {
            if (is_array($values)) {
                foreach ($values as $attr => $val) {
//                    if (is_array($val)) {
//                        throw new \Exception('DtoValidate. Тройная вложенность не поддреживается');
//                    }
                    if (!is_int($attr) && !array_key_exists($attribute, $attributesInRule) && !array_key_exists($attr, $attributesInRule[$attribute])) {
                        if (is_array($val)) {
                            throw new Exception('dtoCheckCountField. Тройная вложенность не поддреживается');
                        }
                        Log::error(
                            'dtoCheckCountField. Не все валидируются (array)',
                            [
                                'extra' => [
                                    'attributesNotRules' => $attribute . '->' . $attr,
                                    'dto' => get_class($dto),
                                    'dtoData' => $dto->toArray(),
                                    'rulesAttr' => array_keys($this->rules()),
                                ],
                            ]
                        );
                        throw new Exception('dtoCheckCountField. Не все валидируются (array)');
                    }
                }
            }
        }
    }


    /**
     * @param Dto $dto
     * @param array|null $data
     * @return Dto
     */
    protected function prepareValuesRecursive(Dto $dto, ?array $data = null): Dto
    {
        foreach (array_keys($dto->toArray()) as $attr) {
            if (is_array($dto->$attr) && array_key_exists(0, $dto->$attr) && $dto->$attr[0] instanceof Dto) {
                foreach ($dto->$attr as $key => $item) {
                    $dto->$attr[$key] = $this->prepareValuesRecursive($dto->$attr[$key], $data[$attr][$key]);
                }
            } elseif ($dto->$attr instanceof Carbon) {
                $dto->$attr = Carbon::parse($data[$attr]);
            } elseif ($dto->$attr instanceof Dto) {
                $this->prepareValuesRecursive($dto->$attr, $data[$attr]);
            } else {
                $dto->$attr = $data[$attr];
            }
        }
        return $dto;
    }
}

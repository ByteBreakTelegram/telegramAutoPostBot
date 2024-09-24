<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\Traits\TableName;
use Exception;
use LogicException;

/**
 * Class Model
 * @method static factory()
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use TableName;

    /**
     * @var array
     */
    protected $casts = []; // @phpstan-ignore-line

    protected function asJson(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function saveOrFail(array $options = []): bool
    {
        throw new Exception('Используй метод saveOrException');
    }

    public function saveOrException(array $options = []): bool
    {
        return $this->save($options) ?: throw new Exception('Model error save to DB: ' . static::class);
    }
}
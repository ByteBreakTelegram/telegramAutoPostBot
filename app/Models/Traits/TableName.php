<?php

declare(strict_types=1);

namespace App\Models\Traits;

trait TableName
{
    protected static array $tableNames = [];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        if (!array_key_exists(static::class, static::$tableNames)) {
            static::$tableNames[static::class] = (new static())->getTable();
        }
        return static::$tableNames[static::class];
    }
}

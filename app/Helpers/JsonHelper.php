<?php

declare(strict_types=1);


namespace App\Helpers;


use stdClass;

class JsonHelper
{
    /**
     * @param mixed $string
     * @return boolean
     */
    public static function isJson(mixed $string): bool
    {
        if (!is_string($string) || ctype_digit($string) || is_numeric($string)) {
            return false;
        }

        return json_decode($string) instanceof stdClass
            && (json_last_error() == JSON_ERROR_NONE);
    }
}
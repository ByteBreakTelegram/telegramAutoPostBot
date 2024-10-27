<?php

declare(strict_types=1);

namespace App\Helpers;

class StringHelper
{
    /**
     * @param string $text
     * @return boolean
     */
    public static function transliterate(string $text): string
    {
        // Массив для транслитерации кириллических букв
        $translit = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'kh',
            'ц' => 'ts',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            ' ' => ''
        ];

        // Преобразуем текст в нижний регистр
        $text = mb_strtolower($text);

        // Транслитерация и удаление пробелов
        $text = strtr($text, $translit);

        // Удаляем все, кроме латинских букв
        $text = preg_replace('/[^a-z]/', '', $text);

        return $text;
    }
}
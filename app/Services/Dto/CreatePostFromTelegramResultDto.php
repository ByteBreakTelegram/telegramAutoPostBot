<?php

declare(strict_types=1);


namespace App\Services\Dto;


use App\Components\Dto\Dto;
use Exception;

class CreatePostFromTelegramResultDto extends Dto
{
    public string $codeChannel;
    public string $postText;
    public int $priority;
    public array $errors;


    /**
     * Формируем текст для возврата в телеграм
     * @return string
     * @throws Exception
     */
    public function getTextForTelegram(): string
    {
        $result = [];
        $result[] = $this->codeChannel;
        $result[] = 'Приоритет: ' . ($this->keyExist('priority') ? $this->priority : 0);
        if ($this->keyExist('errors')) {
            $result[] = 'Errors: ' . implode(';', $this->errors);
        }
        $result[] = '_';
        if ($this->keyExist('postText')) {
            $result[] = $this->postText;
        }
        return implode("\n", $result);
    }
}
<?php

declare(strict_types=1);

namespace App\Jobs\Core;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Максимальное время выполнения
     *
     * @var int
     */
//    public int $timeout = 30;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * Максимальное количество разрешенных необработанных исключений.
     *
     * @var int
     */
    public int $maxExceptions = 1;


    public bool $failOnTimeout = true;

    /**
     * Задать временной предел попыток выполнить задания.
     * То есть через указанное время задача не должна повторно пытаться выполнится
     * @return DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(24);
    }
    /**
     * Секунды через которые задача снимает блокировку повторного выполнения
     * @return int
     */
    protected function expireAfter(): int
    {
        return $this->timeout + 5; // @phpstan-ignore-line
    }

    /**
     * Секунды через которые задача повторно запускается в job
     * @return int
     */
    protected function releaseAfter(): int
    {
        return (int) floor($this->timeout / 3); // @phpstan-ignore-line
    }
}

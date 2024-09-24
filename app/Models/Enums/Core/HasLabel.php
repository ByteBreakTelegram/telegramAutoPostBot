<?php

declare(strict_types=1);

namespace App\Models\Enums\Core;

/**
 * Interface HasLabel
 */
interface HasLabel
{
    /**
     * @return string
     */
    public function label(): string;
}
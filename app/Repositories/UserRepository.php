<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Components\Repository\Repository;
use App\Models\User;

class UserRepository extends Repository
{

    protected function getModelClass(): string
    {
        return User::class;
    }
}
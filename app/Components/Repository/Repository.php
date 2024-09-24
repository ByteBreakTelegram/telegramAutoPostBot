<?php

declare(strict_types=1);


namespace App\Components\Repository;


abstract class Repository
{

    public function find(mixed $id)
    {
        return $this->getModelClass()::find($id);
    }


    abstract protected function getModelClass(): string;
}
<?php

declare(strict_types=1);

namespace Lea\Core\Service;

abstract class Service
{
    public function __construct($repository = null)
    {
        $this->repository = $repository;
    }
}
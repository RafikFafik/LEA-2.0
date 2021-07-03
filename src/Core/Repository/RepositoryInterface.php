<?php

declare(strict_types=1);

namespace Lea\Core\Repository;

interface RepositoryInterface
{
    public function save(object $object);
    public function findById(int $id);
    public function findList();
}

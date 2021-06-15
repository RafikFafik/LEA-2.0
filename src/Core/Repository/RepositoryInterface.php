<?php

declare(strict_types=1);

namespace Lea\Core\Repository;

interface RepositoryInterface
{
    public function save(object $object);
    public static function findById(int $id, object $object);
    public static function getList($object);
}

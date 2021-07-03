<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;

final class RoleRepository extends Repository
{
    public function findListByRoleId(int $role_id): iterable
    {
        $list = $this->getRecordsData($role_id, 'role_id', $this->object);

        return $list;
    }
}

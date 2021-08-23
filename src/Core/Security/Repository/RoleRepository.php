<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;

final class RoleRepository extends Repository
{
    public function findListByRoleId(int $role_id): iterable
    {
        $constraints = ['role_id' => $role_id ];
        $list = $this->getListDataByConstraints($this->object, $constraints);

        return $list;
    }
}

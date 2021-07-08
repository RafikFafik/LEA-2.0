<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Security\Entity\User;

final class UserRepository extends Repository
{
    public function findByEmail(string $email): User
    {
        $result = $this->getRecordData($email, 'email');

        return $result;
    }

    public function findByToken(string $token): User
    {
        $result = $this->getRecordData($token, 'token');

        return $result;
    }

    public function findByRoleId(int $role_id): User
    {
        $object = $this->getRecordData($role_id, 'role_id');

        return $object;
    }

    public function findListByRoleId(int $role_id): iterable
    {
        $constraints['role_id'] = $role_id;
        $list = $this->getListDataByConstraints($this->object, $constraints);

        return $list;
    }

    public function findListDataByRoleIds(array $role_ids): iterable
    {
        $constraints['role_id_IN'] = $role_ids;
        $result = $this->getListDataByConstraints($this->object, $constraints);

        return $result;
    }
}

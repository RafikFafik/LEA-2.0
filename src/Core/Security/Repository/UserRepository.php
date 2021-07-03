<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Security\Entity\User;

final class UserRepository extends Repository
{
    public function findByEmail(string $email): User
    {
        $result = $this->getRecordData($this->object, $email, 'email');

        return $result;
    }

    public function findByToken(string $token): User
    {
        $result = $this->getRecordData($this->object, $token, 'token');

        return $result;
    }

    public function findListDataByRoleIds(array $role_ids): iterable
    {
        $constraints['role_id_IN'] = $role_ids;
        $result = $this->getListDataByConstraints($this->object, $constraints);

        return $result;
    }
}

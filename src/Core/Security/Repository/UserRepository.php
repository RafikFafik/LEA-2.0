<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\Security\Entity\User;

final class UserRepository extends Repository
{
    public static function findByEmail(string $email): User
    {
        $result = self::getRecordData(new User, $email, 'email');

        return $result;
    }

    public static function findByToken(string $token): User
    {
        $result = self::getRecordData(new User, $token, 'token');

        return $result;
    }

    public function findListDataByRoleIds(array $role_ids): iterable
    {
        $constraints['role_id_IN'] = $role_ids;
        $result = $this->getListDataByConstraints(new User, $constraints);

        return $result;
    }
}

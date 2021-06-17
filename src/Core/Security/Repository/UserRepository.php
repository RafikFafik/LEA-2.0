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
}

<?php

namespace Lea\Module\Security\Service;

use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Entity\User;

final class AuthorizedUserService implements ServiceInterface
{
    private static $user;

    public static function setAuthorizedUser(User $user): void
    {
        self::$user = $user;
    }

    public static function getAuthorizedUserId(): int
    {
        return self::$user->getId();
    }
}

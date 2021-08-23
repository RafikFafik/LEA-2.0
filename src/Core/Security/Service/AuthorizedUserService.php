<?php

namespace Lea\Core\Security\Service;

use Lea\Core\Security\Entity\User;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Exception\UserAlreadyAuthorizedException;

final class AuthorizedUserService implements ServiceInterface
{
    private static $user = null;

    public static function userIsAuthorized(): bool
    {
        return self::$user === null ? true : false;
    }

    public static function setAuthorizedUser(User $user): void
    {
        /* Protection against re-setting of authorized user  */
        if (self::$user)
            throw new UserAlreadyAuthorizedException();
        self::$user = $user;
    }

    public static function getAuthorizedUserId(): ?int
    {
        if (!self::$user)
            $user_id = null;
        else
            $user_id = self::$user->getId();

        return $user_id;
    }
    
    public static function getAuthorizedUserRoleId(): ?int
    {
        if (!self::$user)
            $role_id = null;
        $role_id = self::$user->getRoleId();
    
        return $role_id;
    }
}

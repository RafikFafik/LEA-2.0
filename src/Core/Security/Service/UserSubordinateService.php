<?php

namespace Lea\Module\Security\Service;

use Lea\Core\Security\Entity\User;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\RoleRepository;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\ResourceNotExistsException;

final class UserSubordinateService implements ServiceInterface
{
    public function findSubordinateUsersRecursive(int $role_id): iterable
    {
        try {
            $subroles = RoleRepository::getListByField('role_id', $role_id);
            foreach ($subroles as $subrole) {
                $role_id = $subrole->getId();
                $user = UserRepository::getByField(new User, 'role_id', $role_id);
                $role_id = $user->getRoleId();
                $user->custom_field = 2137;
                $user->subordinates = $this->findSubordinateUsersRecursive($role_id);
                $users[] = $user;
            }
        } catch (ResourceNotExistsException $e) {
            return [];
        }
        return $users ?? [];
    }
}

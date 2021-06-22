<?php

namespace Lea\Module\Security\Service;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
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
                $user->subordinates = $this->findSubordinateUsersRecursive($role_id);
                $users[] = $user;
            }
        } catch (ResourceNotExistsException $e) {
            return [];
        }
        return $users ?? [];
    }

    public function findSubordinateUsersFlat(int $role_id): iterable
    {
        $roles = $this->getSubordinateRoles($role_id);
        foreach($roles as $role) {
            $role_ids[] = $role->getId();
        }
        if(!isset($role_ids))
            return [];

        $repository = new UserRepository();
        $users = $repository->findListDataByRoleIds($role_ids);

        return $users;
    }

    public function getSubordinateRoles(int $role_id): iterable
    {
        try {
            $subroles = RoleRepository::getListByField('role_id', $role_id);
            foreach ($subroles as $subrole) {
                $subsubroles = $this->getSubordinateRoles($subrole->getId());
            }
        } catch (ResourceNotExistsException $e) {
            return [];
        }

        return array_merge($subroles ?? [], $subsubroles ?? []);
    }

    private function nestedToFlatArray(iterable $users): iterable
    {
        foreach ($users as $user) {
        }

        return $users;
    }

    function flatten($arr)
    {
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
        return iterator_to_array($it, true);
    }
}

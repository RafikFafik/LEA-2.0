<?php

declare(strict_types=1);

namespace Lea\Core\Security\Service;

use Lea\Core\Service\Service;
use Lea\Core\Security\Repository\RoleRepository;
use Lea\Core\Security\Repository\UserRepository;

final class CommercialAdvisorService extends Service
{
    public function getCommercialAdvisors(int $role_id): iterable
    {
        $user_repository = new UserRepository();
        $list = $user_repository->findListByRoleId($role_id);

        return $list;
    }
}

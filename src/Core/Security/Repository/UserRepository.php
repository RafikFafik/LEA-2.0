<?php

declare(strict_types=1);

namespace Lea\Core\Security\Repository;

use Lea\Core\Repository\Repository;
use Lea\Core\SecurityModule\Entity\User;

final class UserRepository extends Repository
{
    private $entity;

    public function __construct()
    {
        $this->entity = new User();
        parent::__construct($this->entity);
    }

    public static function findByEmail(string $email): User
    {
        $result = self::getRecordData(new User, $email, 'email');

        return $result;
    }
}

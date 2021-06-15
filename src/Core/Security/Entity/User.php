<?php

declare(strict_types=1);

namespace Lea\Core\SecurityModule\Entity;

use Lea\Core\Entity\Entity;

class User extends Entity
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $token;
    
    /**
     * @var int
     */
    private $role_id;
}

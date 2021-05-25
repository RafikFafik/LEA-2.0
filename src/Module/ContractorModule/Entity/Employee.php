<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class Employee extends Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $surname;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;
}

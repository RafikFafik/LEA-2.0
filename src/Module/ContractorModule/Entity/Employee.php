<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class Employee extends Entity
{
    protected $name;
    protected $surname;
    protected $email;
    protected $phone;
}

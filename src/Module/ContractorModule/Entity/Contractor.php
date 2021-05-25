<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;


use Lea\Core\Entity\Entity;
use Lea\Module\ContractorModule\Entity\Address;
use Lea\Module\ContractorModule\Entity\Employee;

class Contractor extends Entity
{
    /**
     * @var string
     */
    protected $shortname;
    /**
     * @var string
     */
    protected $fullname;

    /**
     * @var string
     */
    protected $nip;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var iterable<Address>
     */
    protected $address;

    /**
     * @var iterable<Employee>
     */
    protected $employees;
}

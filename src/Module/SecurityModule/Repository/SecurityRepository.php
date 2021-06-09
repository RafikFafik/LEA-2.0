<?php

declare(strict_types=1);

namespace Lea\Module\SecurityModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Module\ContractorModule\Entity\Contractor;

final class SecurityRepository extends Repository
{
    private $entity;

    public function __construct(array $params)
    {
        $this->entity = new Contractor();
        parent::__construct($this->entity);
    }
}

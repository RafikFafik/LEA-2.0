<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Module\ContractorModule\Entity\Contractor;

final class LibraryAdministrativeDivisionRepository extends Repository
{
    private $entity;

    public function __construct(array $params)
    {
        $this->entity = new Contractor();
        parent::__construct($this->entity);
    }
}

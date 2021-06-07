<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Core\Repository\Repository;
use Lea\Module\OfferModule\Entity\Offer;

final class OfferRepository extends Repository
{
    private $entity;

    public function __construct()
    {
        $this->entity = new Offer();
        parent::__construct($this->entity);
        
    }
}

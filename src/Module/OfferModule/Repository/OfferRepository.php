<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Module\OfferModule\Entity\Offer;

final class OfferRepository extends DatabaseManager implements RepositoryInterface
{
    private $entity;

    public function __construct()
    {
        $this->entity = new Offer();
        parent::__construct($this->entity);
    }

    public static function getById(int $id)
    {
        $instance = new static(new DatabaseManager(new Offer()));
        $res = $instance->getRecordData(new Offer, $id);

        return $res;
    }

    public function save(object $object)
    {
        $this->db->insertRecordData($object);
    }

    public function update(): void
    {
    }
}

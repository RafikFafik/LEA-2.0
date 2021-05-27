<?php

declare(strict_types=1);

namespace Lea\Module\OfferModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Module\OfferModule\Entity\Offer;
use Lea\Core\Entity\Entity;

final class OfferRepository extends DatabaseManager implements RepositoryInterface
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManager();
    }

    public function getById(int $id)
    {
        $res = $this->db->getRecordData(new Offer, $id);

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

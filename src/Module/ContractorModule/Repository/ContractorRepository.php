<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Database\DatabaseManager;
use Lea\Core\Repository\RepositoryInterface;
use Lea\Module\ContractorModule\Entity\Contractor;

final class ContractorRepository extends DatabaseManager implements RepositoryInterface
{
    public function __construct()
    {
        $this->db = new DatabaseManager();
    }

    public function getById(int $id)
    {
        $res = $this->db->getRecordData(new Contractor, $id);

        return $res;
    }

    public function save(object $object)
    {
    }
}

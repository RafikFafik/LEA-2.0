<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Module\ContractorModule\Entity\Contractor;

final class ContractorRepository extends DatabaseManager implements RepositoryInterface
{
    public function __construct()
    {
    }

    public function getById(int $id)
    {
        // $res = $this->db->getRecordData(new Contractor, $id);

        // return $res;
    }

    public function post(object $data)
    {

    }

    public function update(): void
    {
    }
}

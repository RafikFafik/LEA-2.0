<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Module\ContractorModule\Entity\Contractor;


final class ContractorRepository extends DatabaseManager implements RepositoryInterface
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManager();
    }

    public function getById(int $id)
    {
        $res = $this->db->getRecordData(new Contractor, $id);

        return $res;
    }

    public function post()
    {
        // $this->db->insertRecordData()
    }

    public function update(): void
    {
    }
}

<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\RepositoryInterface;
use Lea\Core\Database\DatabaseManager;
use Lea\Module\ContractorModule\Entity\Contractor;


final class ContractorRepository implements RepositoryInterface
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManager();
    }

    public function getById()
    {
        $res = $this->db->getRecordData(new Contractor, 1);

        return $res;
    }

    public function post($_POST): void
    {
        
    }

    public function update(): void
    {
    }
}

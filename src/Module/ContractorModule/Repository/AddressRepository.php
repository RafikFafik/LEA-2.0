<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\Repository;
use Lea\Module\ContractorModule\Entity\Address;

final class AddressRepository extends Repository
{
    public function findMainHeadquarterByContractorId(int $contractor_id): Address
    {
        $result = $this->getRecordData($contractor_id, 'contractor_id');

        return $result;
    }
}

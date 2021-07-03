<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Repository;

use Lea\Core\Repository\Repository;

final class LibraryAdministrativeDivisionRepository extends Repository
{
    public function findByPostcode(string $postcode): object
    {
        $object = $this->getRecordData($postcode, 'postcode');

        return $object;
    }
}

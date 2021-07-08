<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Service;

use Lea\Core\Service\Service;

class ContractorService extends Service
{
    public function getView(): iterable
    {
        $list = $this->repository->findContractorList();

        return $list;
    }
}

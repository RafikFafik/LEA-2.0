<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Service;

use Lea\Core\Serializer\Normalizer;
use Lea\Core\Service\Service;
use Lea\Core\View\ViewGenerator;

class ContractorService extends Service
{
    public function getView(): iterable
    {
        $list = $this->repository->findContractorList();
        $view = new ViewGenerator($this->repository);
        $array = Normalizer::denormalizeList($list);

        return $view->formatPagination($array);
    }
}

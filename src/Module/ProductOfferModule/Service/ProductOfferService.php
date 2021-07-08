<?php

declare(strict_types=1);

namespace Lea\Module\ProductOfferModule\Service;

use Lea\Response\Response;
use Lea\Core\Service\Service;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

class ProductOfferService extends Service
{
    public function getFullData($id): void
    {
        $contractor_repository = new ContractorRepository();
        $obj = $this->repository->findById($id);
        $contractor = $contractor_repository->findById($obj->getContractorId());
        $obj->contractor_fullname = $contractor->getFullName();
        $res = Normalizer::denormalize($obj);
        Response::ok($res);
    }
}

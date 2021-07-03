<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\ContractorModule\Entity\Contractor;

class ContractorCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $contractorRepository = new ContractorRepository;
                $list = $contractorRepository->getList(new Contractor);
                $res = Normalizer::denormalizeList($list);

                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                $contractor = new ContractorRepository;
                $resource_id = $contractor->save($data);

                // debug
                $contractorRepository = new ContractorRepository;
                $object = $contractorRepository->findById($resource_id, new Contractor);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                // Response::noContent();
            case "DELETE":
                Response::methodNotAllowed();
            default:
                Response::methodNotAllowed();
        }
    }
}

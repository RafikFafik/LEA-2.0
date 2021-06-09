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
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $contractorRepository = new ContractorRepository($this->params);
                $list = $contractorRepository->getList(new Contractor);
                $res = Normalizer::denormalizeList($list);

                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                $contractor = new ContractorRepository($this->params);
                $resource_id = $contractor->save($data);

                // debug
                $contractorRepository = new ContractorRepository($this->params);
                $object = $contractorRepository->getById($resource_id, new Contractor);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                // Response::noContent();
            case "DELETE":
                Response::ok("Deleteing not implemented yet");
            default:
                Response::methodNotAllowed();
        }
    }
}

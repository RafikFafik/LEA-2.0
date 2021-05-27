<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Entity\Contractor;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

class ContractorController implements ControllerInterface
{
    private $request;

    function __construct(Request $request, array $params = NULL)
    {
        $this->request = $request;
        $this->params = $params;
    }

    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                $contractorRepository = new ContractorRepository();
                $object = $contractorRepository->getById($this->params['id']);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
            case "POST":
                $contractor = new ContractorRepository();
                $data = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                $contractor->save($data);
                Response::noContent();
            case "DELETE":
                Response::ok("Not implemented yet");
            default:
                Response::methodNotAllowed();
        }
    }
}

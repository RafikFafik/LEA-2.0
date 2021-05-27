<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\ContractorModule\Entity\Contractor;

class ContractorCollectionController implements ControllerInterface
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
                $res = $contractorRepository->getById($this->params['id']);
                Response::ok($res);
            case "POST":
                $data = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                $contractor = new ContractorRepository();
                $contractor->save($data);
                Response::noContent();
            case "DELETE":
                Response::ok("Deleteing not implemented yet");
            default:
                Response::methodNotAllowed();
        }
    }
}

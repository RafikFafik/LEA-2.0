<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Database\DatabaseException;
use Lea\Core\Exception\ResourceNotExistsException;
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
                try {
                    $contractorRepository = new ContractorRepository($this->params);
                    $object = $contractorRepository->getById($this->params['id'], new Contractor);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
            case "POST":
                try {
                    $contractorRepository = new ContractorRepository($this->params);
                    $object = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                    $affected_rows = $contractorRepository->updateById($object, $this->params['id']);

                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }



                $object = $contractorRepository->getById($this->params['id'], new Contractor);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
            case "DELETE":
                Response::notImplemented();
            default:
                Response::methodNotAllowed();
        }
    }
}

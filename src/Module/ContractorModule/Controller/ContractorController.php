<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Database\DatabaseException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\ContractorModule\Entity\Contractor;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

class ContractorController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "GET":
                try {
                    $contractorRepository = new ContractorRepository;
                    $object = $contractorRepository->findById($this->params['id'], new Contractor);
                    $res = Normalizer::denormalize($object);
                    Response::ok($res);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                break;
            case "POST":
                try {
                    $contractorRepository = new ContractorRepository;
                    $object = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                    $affected_rows = $contractorRepository->updateById($object, $this->params['id']);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                $object = $contractorRepository->findById($this->params['id'], new Contractor);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                break;
            case "PUT":
                try {
                    $contractorRepository = new ContractorRepository;
                    $object = Normalizer::normalize($this->request->getPayload(), Contractor::getNamespace());
                    $affected_rows = $contractorRepository->updateById($object, $this->params['id']);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest();
                }
                $object = $contractorRepository->findById($this->params['id'], new Contractor);
                $res = Normalizer::denormalize($object);
                Response::ok($res);
                break;
            case "DELETE":
                $contractorRepository = new ContractorRepository;
                $contractorRepository->removeById($this->params['id']);
                Response::noContent();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

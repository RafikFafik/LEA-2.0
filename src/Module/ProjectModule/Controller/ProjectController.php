<?php

namespace Lea\Module\ProjectModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Serializer\Normalizer as SerializerNormalizer;
use Lea\Module\ContractorModule\Repository\ContractorEmployeeRepository;
use Lea\Module\ProjectModule\Repository\ProjectRepository;
use Lea\Module\ProjectModule\Service\ProjectService;
use Normalizer;

class ProjectController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new ProjectRepository();

        switch ($this->http_method) {
            case "GET":
                $service = new ProjectService();
                $result = $service->getById($this->params['id']);
                
                Response::ok($result);
            case "POST":
            case "PUT":
                $this->updateResource();
                break;
            case "DELETE":
                $this->deleteResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

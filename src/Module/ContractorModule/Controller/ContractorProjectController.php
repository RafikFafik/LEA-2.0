<?php

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\ProjectModule\Repository\ProjectRepository;
use Lea\Response\Response;

class ContractorProjectController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch($this->http_method) {
            case "GET":
                $project_repository = new ProjectRepository();
                $list = $project_repository->findFlatList(['contractor_id' => $this->params['id']]);
                $result = Normalizer::denormalizeSpecificFieldsList($list, ['id', 'name']);
                Response::ok($result);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Repository\ContractorRepository;
use Lea\Core\Serializer\Normalizer;

class ContractorNutshellCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "GET":
                $repository = new ContractorRepository;
                $nested = isset($this->params['nested']) && filter_var($this->params['nested'], FILTER_VALIDATE_BOOLEAN);
                $list = $repository->findList(['active' => true], [], $nested);
                $needles = ['shortname', 'employees', 'name', 'surname', 'id'];
                $result = Normalizer::denormalizeSpecificFieldsList($list, $needles);
                // $result = Normalizer::filterSpecificFieldsFromArrayList($result, ['id', 'shortname']);
                // $result = Normalizer::mapKeyOfArrayList();
                Response::ok($result);
            default:
                Response::methodNotAllowed();
        }
    }
}

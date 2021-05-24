<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

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
                $contractor = new ContractorRepository();
                $contractor->post($_POST);
            case "DELETE":
                Response::ok("Not implemented yet");
            default:
                Response::methodNotAllowed();
        }
    }
}

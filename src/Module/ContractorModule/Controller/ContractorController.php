<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Controller;

use Lea\Request\Request;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Repository\OfferRepository;
use Lea\Module\ContractorModule\Repository\ContractorRepository;

class ContractorController implements ControllerInterface
{
    private $request;
    private $methods;

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
                $res = $contractorRepository->getById();
                Response::ok($res);
            case "POST":
                $contractor = new ContractorRepository();
                $contractor->post($_POST);
            default:
                Response::methodNotAllowed();
        }
    }
}

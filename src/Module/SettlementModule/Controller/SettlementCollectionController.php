<?php

namespace Lea\Module\SettlementModule\Controller;

use Lea\Response\Response;
use Lea\Core\View\ViewGenerator;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\SettlementModule\Service\SettlementService;
use Lea\Module\SettlementModule\Repository\SettlementRepository;

class SettlementCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new SettlementRepository();
        $service = new SettlementService($this->repository);

        switch ($this->http_method) {
            case "GET":
                $view = new ViewGenerator(new SettlementRepository());
                if (isset($this->params['state']))
                    $result = $view->getView($this->params['state']);
                else
                    $result = $view->getView('active');
                Response::ok($result);
            case "POST":
                $this->postResource($this->repository);
                Response::noContent();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

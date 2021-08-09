<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\View\ViewGenerator;
use Lea\Module\OfferModule\Repository\OfferRepository;
use Lea\Module\OfferModule\Service\OfferService;
use Lea\Response\Response;

class OfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new OfferRepository();
        $service = new OfferService($this->repository);

        switch ($this->http_method) {
            case "GET":
                $view = new ViewGenerator(new OfferRepository(true));
                if (isset($this->params['state']))
                    $result = $view->getView($this->params['state']);
                else
                    $result = $view->getView('active');
                Response::ok($result);
                // Response::ok($service->getView());
            case "POST":
                $this->postResource($this->repository);
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

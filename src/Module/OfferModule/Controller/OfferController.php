<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Service\OfferService;
use Lea\Module\OfferModule\Repository\OfferRepository;

class OfferController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new OfferRepository();
        $service = new OfferService($this->repository);

        switch ($this->http_method) {
            case "GET":
                $result = $service->getOfferById($this->params['id']);
                Response::ok($result);
            case "POST":
                $this->updateResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

<?php

namespace Lea\Module\OfferModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Serializer\Normalizer;
use Lea\Module\OfferModule\Repository\OfferRepository;
use Lea\Module\OfferModule\Service\OfferService;
use Lea\Response\Response;

class OfferCollectionController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        $this->repository = new OfferRepository();

        switch ($this->http_method) {
            case "GET":
                $service = new OfferService($this->repository);
                $list = $service->getView();
                $result = Normalizer::denormalizeList($list);
                Response::ok($result);
            case "POST":
                $this->postResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }
}

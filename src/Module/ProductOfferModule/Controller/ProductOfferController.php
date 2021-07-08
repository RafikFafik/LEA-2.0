<?php

namespace Lea\Module\ProductOfferModule\Controller;

use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\ProductOfferModule\Service\ProductOfferService;

class ProductOfferController extends Controller implements ControllerInterface
{
    public function init(): void
    { 
        $Repository = $this->getRepositoryClass();
        $this->repository = new $Repository();

        switch ($this->http_method) {
            case "GET":
                if ($this->isCollectionController())
                    $this->getCollection();
                else
                    $this->getResource();
                break;
            case "POST":
            case "PUT":
                if ($this->isCollectionController())
                    $this->postResource();
                else
                    $this->updateResource();
                break;
            case "DELETE":
                if ($this->isCollectionController())
                    Response::methodNotAllowed();
                else
                    $this->deleteResource();
                break;
            default:
                Response::methodNotAllowed();
        }
    }

    public function getResource(): void
    {
        $service = new ProductOfferService($this->repository);
        $service->getFullData($this->params['id']);
    }
}

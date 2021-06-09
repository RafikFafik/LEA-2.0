<?php

namespace Lea\Module\SecurityModule\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;
use Lea\Core\Serializer\Normalizer;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\OfferModule\Repository\OfferRepository;

class RegistrationController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "POST":
                
            default:
                Response::methodNotAllowed();
        }
    }
}

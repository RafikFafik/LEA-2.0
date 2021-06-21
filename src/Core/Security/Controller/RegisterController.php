<?php

namespace Lea\Module\Security\Controller;

use Exception;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Module\Security\Service\LoginService;
use Lea\Module\Security\Service\RegisterService;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\InvalidCredentialsException;

class RegisterController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "POST":
                    $service = new RegisterService;
                    $service->register($this->request->getPayload());
                    Response::accepted();
            default:
                Response::methodNotAllowed();
        }
    }
}

<?php

namespace Lea\Module\Security\Controller;

use Exception;
use Lea\Response\Response;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\EmailNotSentException;
use Lea\Module\Security\Service\LoginService;
use Lea\Module\Security\Service\RegisterService;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\InvalidCredentialsException;

class RegisterController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "POST":
                    $service = new RegisterService;
                    try {
                        $service->register($this->request->getPayload());
                    } catch (EmailNotSentException $e) {
                        Response::accepted("Caution! Email not sent, but 202 status code");
                    }
                    Response::accepted();
            default:
                Response::methodNotAllowed();
        }
    }
}

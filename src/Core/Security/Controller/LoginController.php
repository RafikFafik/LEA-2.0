<?php

namespace Lea\Module\Security\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\InvalidCredentialsException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\Security\Service\AuthenticationService;

class LoginController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "POST":
                try {
                    $auth = new AuthenticationService;
                    $token = $auth->login($this->request->payload['email'], $this->request->payload['password']);
                    Response::ok($token);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Invalid credentials");
                } catch (InvalidCredentialsException $e) {
                    Response::badRequest("Invalid credentials");
                }
            default:
                Response::methodNotAllowed();
        }
    }
}

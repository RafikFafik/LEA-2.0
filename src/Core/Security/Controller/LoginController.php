<?php

namespace Lea\Core\Security\Controller;

use Lea\Core\Controller\Controller;
use Lea\Response\Response;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\InactiveAccountException;
use Lea\Core\Exception\InvalidCredentialsException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Security\Service\LoginService;

class LoginController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "POST":
                try {
                    $auth = new LoginService;
                    $userdata = $auth->login($this->request->payload['email'], $this->request->payload['password'], $this->request->payload['mobile_app_token'] ?? null);
                    Response::ok($userdata);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Invalid credentials");
                } catch (InvalidCredentialsException $e) {
                    Response::badRequest("Invalid credentials");
                } catch (InactiveAccountException $e) {
                    Response::badRequest("Account not activated, check email-box or consider requesting another invitation email");
                }
            default:
                Response::methodNotAllowed();
        }
    }
}

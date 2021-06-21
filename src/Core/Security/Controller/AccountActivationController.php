<?php

namespace Lea\Module\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Validator\Validator;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Module\Security\Service\AccountActivationService;

class AccountActivationController extends Controller implements ControllerInterface
{
    public function init()
    {
        switch ($this->request->method()) {
            case "POST":
                $data = $this->request->getPayload();
                Validator::validateAccountActivationParams($data);
                Validator::validatePasswordStrength($data['password']);
                $service = new AccountActivationService;
                try {
                    $service->activateAccount($data['token'], $data['password']);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Invalid Token");
                }
                Response::accepted();
            default:
                Response::methodNotAllowed();
        }
    }
}

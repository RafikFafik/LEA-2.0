<?php

namespace Lea\Core\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Validator\Validator;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Security\Service\PasswordService;

class ResetPasswordController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "POST":
                $data = $this->request->getPayload();
                Validator::validateEmail($data['email']);
                $service = new PasswordService;
                $service->sendPasswordResetEmail($data['email']);


                Response::accepted();
            default:
                Response::methodNotAllowed();
        }
    }
}

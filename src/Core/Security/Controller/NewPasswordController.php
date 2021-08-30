<?php

namespace Lea\Core\Security\Controller;

use Lea\Response\Response;
use Lea\Core\Validator\Validator;
use Lea\Core\Controller\Controller;
use Lea\Core\Controller\ControllerInterface;
use Lea\Core\Exception\InactiveAccountException;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Security\Service\PasswordService;

class NewPasswordController extends Controller implements ControllerInterface
{
    public function init(): void
    {
        switch ($this->request->method()) {
            case "POST":
                $service = new PasswordService;
                $data = $this->request->getPayload();
                Validator::validatePasswordStrength($data['password']);
                try {
                    $service->resetPassword($data['token'], $data['password']);
                } catch (ResourceNotExistsException $e) {
                    Response::badRequest("Invalid token");
                } catch(InactiveAccountException $e) {
                    Response::badRequest("Inactive account");
                }
                Response::noContent();
            default:
                Response::methodNotAllowed();
        }
    }
}

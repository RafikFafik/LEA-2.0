<?php

namespace Lea\Module\Security\Service;

use Lea\Response\Response;
use Lea\Core\Security\Entity\User;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Validator\Validator;

final class RegisterService extends AuthenticationService implements ServiceInterface
{
    public function register(array $data): void
    {
        Validator::validateRegisterParams($data);
        try {
            $user = UserRepository::findByEmail($data['email']);
            Response::badRequest("User with given email address already exists");
        } catch (ResourceNotExistsException $e) {
        }

        $user = new User($data);
        $repository = new UserRepository;
        $password = $this->getRandomString(8);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $repository->save($user);
        Response::ok($password);
    }
}

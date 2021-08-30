<?php

namespace Lea\Core\Security\Service;

use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Response\Response;

class AccountActivationService extends AuthenticationService implements ServiceInterface
{
    public function activateAccount(string $token, string $password): void
    {
        $repository = new UserRepository();
        $user = $repository->findByToken($token);
        if($user->getActive())
            Response::badRequest("Account already activated");
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $user->setPassword($hashed_password);
        $user->setActive(true);
        $user->setToken("");
        $repository->save($user);
    }

    public function getActivationToken(): string
    {
        return sha1($this->getRandomString(64) . microtime());
    }
}

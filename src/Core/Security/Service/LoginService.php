<?php

namespace Lea\Module\Security\Service;

use Exception;
use Firebase\JWT\JWT;
use Lea\Response\Response;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\InvalidCredentialsException;

class LoginService extends AuthenticationService implements ServiceInterface
{
    public function login(string $email, string $password): array
    {
        $user = UserRepository::findByEmail($email);
        if (!password_verify($password, $user->getPassword()))
            throw new InvalidCredentialsException();
        $uid = $user->getId();
        $token = $this->generateJWT($email, $uid);

        return ['token' => $token];
    }
}

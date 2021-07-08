<?php

namespace Lea\Core\Security\Service;

use Lea\Core\Playground\NameDay;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Exception\InactiveAccountException;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\InvalidCredentialsException;

final class LoginService extends AuthenticationService implements ServiceInterface
{
    public function login(string $email, string $password): array
    {
        $repository = new UserRepository();
        $user = $repository->findByEmail($email);
        if (!password_verify($password, $user->getPassword()))
            throw new InvalidCredentialsException();
        if($user->getActive() === false)
            throw new InactiveAccountException;
        $uid = $user->getId();
        $token = $this->generateJWT($email, $uid, $user->getRoleId());
        $userdata = [
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'token' => $token,
            'dzis_obchodzimy_imieniny' => [
                NameDay::getNameDay()
            ]
        ];

        return $userdata;
    }
}

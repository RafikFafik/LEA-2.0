<?php

namespace Lea\Core\Security\Service;

use Lea\Core\Exception\InactiveAccountException;
use Lea\Core\Mailer\Mailer;
use Lea\Core\Mailer\MailerBodyProvider;
use Lea\Core\Security\Repository\UserRepository;


final class PasswordService extends AccountActivationService
{
    public function sendPasswordResetEmail(string $email): void
    {
        $ur = new UserRepository();
        $user = $ur->findByEmail($email);
        if ($user->getActive() === 0)
            throw new InactiveAccountException($user->getEmail());


        $fresh_token = $this->getActivationToken();
        $user->setToken($fresh_token);
        $ur->save($user);

        Mailer::sendMail($email, sprintf("Reset hasła w systemie %s", $_ENV['TENANT']), MailerBodyProvider::getResetPasswordBodyMessage($fresh_token, $user->getName(), $user->getSurname(), $_ENV['TENANT']));
    }

    public function resetPassword(string $token, string $password): void
    {
        $repository = new UserRepository();
        $user = $repository->findByToken($token);
        if($user->getActive() == 0)
            throw new InactiveAccountException();
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $user->setPassword($hashed_password);
        $user->setToken("");
        $repository->save($user);
    }
}

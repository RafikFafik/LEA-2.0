<?php

namespace Lea\Core\Security\Service;

use Lea\Response\Response;
use Lea\Core\Mailer\Mailer;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Mailer\MailerBodyProvider;
use Lea\Core\Security\Repository\UserRepository;

final class AccountActivationEmailResendService extends AuthenticationService implements ServiceInterface
{
    public function resendEmail(string $email): void
    {
        $repository = new UserRepository();
        $user = $repository->findByEmail($email);
        $active = $user->getActive();
        if ($active)
            Response::badRequest("Account already activated");
        $token = sha1($this->getRandomString(64) . microtime());
        $user->setToken($token);
        $repository->save($user);
        $name = $user->getName();
        $surname = $user->getSurname();
        $body = MailerBodyProvider::getAccountCreatedBodyMessage($token, $_ENV['TENANT'], $name, $surname);
        $repository->save($user);
        Mailer::sendMail($email, "Nowe konto w systemie " . $_ENV['TENANT'], $body);
    }
}

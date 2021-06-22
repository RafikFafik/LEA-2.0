<?php

namespace Lea\Module\Security\Service;

use Lea\Response\Response;
use Lea\Core\Mailer\Mailer;
use Lea\Core\Validator\Validator;
use Lea\Core\Security\Entity\User;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Mailer\MailerBodyProvider;

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
        $token = sha1($this->getRandomString(64) . microtime());
        $user->setToken($token);
        $user->setActive(false);
        $repository->save($user);
        $body = MailerBodyProvider::getAccountCreatedBodyMessage($token, $_ENV['TENANT'], $user->getName(), $user->getSurname());
        Mailer::sendMail($data['email'], "Nowe konto w systemie " . $_ENV['TENANT'], $body);
        Response::accepted();
    }
}

<?php

namespace Lea\Module\Security\Service;

use Firebase\JWT\JWT;
use Lea\Core\Exception\InvalidCredentialsException;
use Lea\Response\Response;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Repository\UserRepository;

class AuthenticationService implements ServiceInterface
{
    public function __construct()
    {
        $this->key = $_ENV['PRIVATE_KEY'];
        $this->encryption_iv = $_ENV['ENCRYPTION_IV'];
        $this->hashCode = $_ENV['HASH_CODE'];
        $this->cipher = $_ENV['CIPHER'];
    }

    public function login(string $email, string $password): array
    {
        $user = UserRepository::findByEmail($email);
        if(!password_verify($password, $user->getPassword()))
            throw new InvalidCredentialsException();
        $uid = $user->getId();
        $token = $this->generateJWT($email, $uid);

        return ['token' => $token];
    }

    private function generateJWT(string $email, int $uid): string
    {
        $exp = 20; // In minutes
        $payload = [
            "iss" => $_ENV['TENANT'], /* Issuer */
            "aud" => $email, /* Receiver */
            "iat" => time(), /* Time of issue */
            //"nbf" => 1357000000, not before - delay after token activation
            "exp" => strtotime('+' . ($exp + 1) . 'min'), /* Period of validity */
            "uid" => $this->encryptUID($uid),
            "sec" => $exp * 60
        ];
        $token = JWT::encode($payload, $this->key);

        return $token;
    }

    private function encryptUID(int $uid): string
    {
        $front = $this->getRandomString(21);
        // $back = $this->getRandomString(37);

        $encrypted = $this->secureEncrypt($uid);

        return $front . $encrypted;
    }

    private function getRandomString(int $len)
    {
        $total_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $index = rand(0, strlen($total_characters) - 1);
            $randomString .= $total_characters[$index];
        }
        return $randomString;
    }

    private function secureEncrypt(string $to_encrypt): string
    {
        $encrypted = openssl_encrypt($to_encrypt, $this->cipher, $this->key, $options = 0, $this->encryption_iv);

        return $encrypted;
    }
}

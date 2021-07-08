<?php

namespace Lea\Core\Security\Service;

use Firebase\JWT\JWT;
use Lea\Core\Service\ServiceInterface;

abstract class AuthenticationService implements ServiceInterface
{
    public function __construct()
    {
        $this->key = $_ENV['PRIVATE_KEY'];
        $this->encryption_iv = $_ENV['ENCRYPTION_IV'];
        $this->hashCode = $_ENV['HASH_CODE'];
        $this->cipher = $_ENV['CIPHER'];
    }



    protected function generateJWT(string $email, int $uid, int $role_id): string
    {
        $exp = 5000; // In minutes
        $payload = [
            "iss" => $_ENV['TENANT'], /* Issuer */
            "aud" => $email, /* Receiver */
            "iat" => time(), /* Time of issue */
            //"nbf" => 1357000000, not before - delay after token activation
            "exp" => strtotime('+' . ($exp + 1) . 'min'), /* Period of validity */
            "uid" => $this->encryptUID($uid),
            "sec" => $exp * 60,
            "role_id" => $role_id
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

    protected function getRandomString(int $len)
    {
        $total_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $index = rand(0, strlen($total_characters) - 1);
            $randomString .= $total_characters[$index];
        }
        return $randomString;
    }

    protected function secureEncrypt(string $to_encrypt): string
    {
        $encrypted = openssl_encrypt($to_encrypt, $this->cipher, $this->key, $options = 0, $this->encryption_iv);

        return $encrypted;
    }
}

<?php

namespace Lea\Module\Security\Service;

use Exception;
use Firebase\JWT\JWT;
use Lea\Core\Security\Repository\UserRepository;
use Lea\Response\Response;
use Lea\Core\Service\ServiceInterface;
use Lea\Core\Security\Entity\User;

class TokenVerificationService extends AuthenticationService implements ServiceInterface
{
    public function authorize(): User
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION2']) && !empty($_SERVER['HTTP_AUTHORIZATION2']))
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION2'];

        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

        if (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION']))
            Response::unauthorized();

        $TOKEN = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
        if (strtoupper($TOKEN[0]) !== "BEARER" || !isset($TOKEN[1]))
            Response::unauthorized();
        $TOKEN = $TOKEN[1]; /* [0] => "Bearer", [1] => TOKEN */

        try {
            $decoded = (array)JWT::decode($TOKEN, $this->key, array('HS256'));
            $credentials = $this->getCredentialsFromToken($decoded);
            $user = UserRepository::findById($credentials['uid'], new User);
            $fresh_token = $this->generateJWT($credentials['email'], $credentials['uid']);
            Header("Authorization: Bearer $fresh_token");
            Header("Access-Control-Expose-Headers: Authorization");
            AuthorizedUserService::setAuthorizedUser($user);
        } catch (Exception $e) {
            Response::unauthorized();
        }

        return $user;
    }

    private function getCredentialsFromToken(array $token): array
    {
        $data['email'] = $token['aud'];
        $data['uid'] = $this->decryptUID($token['uid']);

        return $data;
    }

    /**
     * UID z doklejonym losowym stringiem na początku o długości 21 i na końcu o długości 37
     */
    private function decryptUID(string $encrypted_uid): int
    {
        // $uid = substr($encrypted_uid, 21, strlen($encrypted_uid)-37);
        $uid = substr($encrypted_uid, 21);

        $decrypted = $this->secureDecrypt($uid);

        return $decrypted;
    }

    private function encryptUID(int $uid): string
    {
        $front = AuthenticationService::getRandomString(21);
        // $back = $this->getRandomString(37);

        $encrypted = $this->secureEncrypt($uid);

        return $front . $encrypted;
    }

    private function secureDecrypt(string $to_decrypt): string
    {
        $decrypted  = openssl_decrypt($to_decrypt, $this->cipher, $this->key, $options = 0, $this->encryption_iv);

        return $decrypted;
    }
}

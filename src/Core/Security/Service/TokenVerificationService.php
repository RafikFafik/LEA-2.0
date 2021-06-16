<?php

namespace Lea\Module\Security\Service;

use Exception;
use Firebase\JWT\JWT;
use Lea\Response\Response;
use Lea\Core\Service\ServiceInterface;

class TokenVerificationService extends AuthenticationService implements ServiceInterface
{
    public function authorize(): void {
        if(isset($_SERVER['HTTP_AUTHORIZATION2']) && !empty($_SERVER['HTTP_AUTHORIZATION2']))
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION2'];

        if(!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION']))
            Response::unauthorized();

        $TOKEN = explode(" ", $_SERVER['HTTP_AUTHORIZATION']); 
        if(strtoupper($TOKEN[0]) !== "BEARER")
            Response::unauthorized();
        $TOKEN = $TOKEN[1]; /* [0] => "Bearer", [1] => TOKEN */
    
        try {
            $decoded = (array)JWT::decode($TOKEN, $this->key, array('HS256'));
            /* Sprawdzenie, czy należy wygenerować nowy TOKEN */
            $credentials = $this->getCredentialsFromToken($decoded);
            $fresh_token = $this->generateNewAuthorizationTokenByEmailAndUID($credentials['email'], $credentials['uid']);
            Header("Authorization: Bearer $fresh_token");
            Header("Access-Control-Expose-Headers: Authorization");
            /* UID używany do dalszej weryfikacji w systemie */
            $this->uid = $credentials['uid'];
            // $this->db->setUser($credentials['uid']); /* Używane do logowania operacji na bazie wykonywanych przez konkretnego USER'a */
        } catch(Exception $e) {
            Response::unauthorized();
        }
    }

    private function getCredentialsFromToken(array $token): array {
        $data['email'] = $token['aud'];
        $data['uid'] = $this->decryptUID($token['uid']);

        return $data;
    }

    private function generateNewAuthorizationTokenByEmailAndUID(string $email, int $uid): string {
        $exp = 20; // W minutach
        $payload = [
            "iss" => "KPFP", /* Issuer - wystawca */
            "aud" => $email, /* Odbiorca */
            "iat" => time(), /* Data i czas wydania */
            //"nbf" => 1357000000, not before - opóźnienie aktywowania tokenu od momentu stworzenia
            "exp" => strtotime('+'.($exp+1).'min'), /* Okres ważności */
            "uid" => $this->encryptUID($uid),
            "sec" => $exp * 60
        ];
        $token = JWT::encode($payload, $this->key);
        
        return $token;
    }

    /**
     * UID z doklejonym losowym stringiem na początku o długości 21 i na końcu o długości 37
     */
    private function decryptUID(string $encrypted_uid): int {
        // $uid = substr($encrypted_uid, 21, strlen($encrypted_uid)-37);
        $uid = substr($encrypted_uid, 21);

        $decrypted = $this->secureDecrypt($uid);

        return $decrypted;
    }

    private function encryptUID(int $uid): string  {
        $front = AuthenticationService::getRandomString(21);
        // $back = $this->getRandomString(37);

        $encrypted = $this->secureEncrypt($uid);

        return $front.$encrypted;
    }

    private function secureDecrypt(string $to_decrypt): string {
        $decrypted  = openssl_decrypt ($to_decrypt, $this->cipher, $this->key, $options = 0, $this->encryption_iv);

        return $decrypted;
    }
}

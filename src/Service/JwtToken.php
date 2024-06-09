<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeImmutable;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken
{

    private const REFRESH_THRESHOLD = 300;

    public function createToken (array $data): string
    {

        $issuedAt = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $expire = $issuedAt->modify('+10 minutes')->getTimestamp(); 
        return JWT::encode([
            'iat' => $issuedAt->format("d/m/Y H:i:s"),  
            'nbf' => $issuedAt->getTimestamp(),  
            'exp' => $expire, 
            'user_data' =>  $data,
        ], JWT_SECRET , 'HS256');
       
    }


    // Va rafraichir mon token à chaque connexion
    public function verifyToken($tokenJWT) 
    {
        try {

            $decoded = JWT::decode($tokenJWT, new Key(JWT_SECRET, 'HS256'));    
            
            if (is_object($decoded->user_data)) {
                $data = get_object_vars($decoded->user_data);
                $this->executeRefreshToken($data);
            }

        } catch (ExpiredException $e) {

            throw new Exception('Token expired');

        } catch (SignatureInvalidException $e) {

            throw new Exception('Invalid token signature');

        } catch (BeforeValidException $e) {

            throw new Exception('Token not valid yet');

        } catch (Exception $e) {

            return json_encode([
                "message" => "Le jeton JWT n'a pas pu être raffraichi",
                "error"  => $e
            ]);
        }
    }



    function isDelayExpired(int $start ): bool {

        $now = time();
        $diffInSeconds = $now - $start;
        if ($diffInSeconds > time() + self::REFRESH_THRESHOLD) {
            // Le délai a expiré
            return true ;
        } else {
            // Le délai est encore valide
            return false ;
        }      
    }



    // Refresh an old Token 
    public function executeRefreshToken(array $datasUser)
    {
        $newJWT = $this->createToken($datasUser);
        $cookie = new CookieHelper();
        return $cookie->setCookie('Token', $newJWT );
    }

}
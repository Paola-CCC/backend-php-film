<?php


declare(strict_types=1);

namespace App\Service;


class CookieHelper
{
    private int $expire = (86400 * 30);

    public function setCookie(string $cookieName, $tokenJWT)
    {
        $this->deleteCookie($cookieName);
        return setcookie($cookieName, $tokenJWT, time() + 3600 + $this->expire,'/');
    } 

    public function getCookie(string $cookieName): ?array
    {
        if (!isset($_COOKIE[$cookieName])) {
            return null;
        } else {
            return $_COOKIE[$cookieName] ;
        }      
    }

    public function deleteCookie(string $cookieName): bool
    {
        return setcookie($cookieName, '', time() - 3600, '/');
    }

}

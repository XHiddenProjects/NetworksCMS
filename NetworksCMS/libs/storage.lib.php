<?php
namespace NetWorks\libs;
include dirname(path: __DIR__).'/init.php';
if((function_exists(function: 'session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
    session_start();
class Storage{
    /**
      * Storage class
      */
    public function __construct() {

    }
    /**
     * Sets a cookie
     * @param string $name Cookie's name
     * @param string $value Cookie's value
     * @param int $time Cookies's expire time. **1 = 1 hour**
     * @param string $path Cookie's path
     * @param string $domain Cookie's domain
     * @param bool $secure HTTPS only
     * @param bool $httponly HTTP only
     * @return bool TRUE if the cookie has been set, else FALSE
     */
    public function setCookie(string $name,string $value,int $time, string $path='/', string $domain='', bool $secure=false, bool $httponly=true): bool{
        return setcookie(name: $name,value: $value,expires_or_options: time()+3600*$time,path: $path, domain: $domain,secure:$secure,httponly: $httponly);
    }
    /**
     * Returns the value of a cookie
     * @param string $name Cookie's name
     * @return mixed Returns the value of the cookie, else null
     */
    public function getCookie(string $name): mixed{
        return $_COOKIE[$name]??null;
    }
    /**
     * Checks if cookie exists
     * @param string $name Cookies name
     * @return bool TRUE if cookie exists, else FALSE
     */
    public function checkCookie(string $name): bool{
        return isset($_COOKIE[$name]);
    }
    /**
     * Sets the sessions value
     * @param string $name sessions name
     * @param mixed $value sessions value
     * @return void
     */
    public function setSession(string $name,mixed $value): void{
        $_SESSION[$name] = $value;
    }
    /**
     * Returns the value of the session
     * @param string $name Sessions name
     * @return mixed Sessions value
     */
    public function getSession(string $name): mixed{
        return $_SESSION[$name]??null;
    }
    /**
     * Checks if sessions exists
     * @param string $name Sessions name
     * @return bool TRUE if exists, else FALSE
     */
    public function checkSession(string $name):bool{
        return isset($_SESSION[$name]);
    }
}
?>
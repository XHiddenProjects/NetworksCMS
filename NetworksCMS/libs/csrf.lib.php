<?php
namespace NetWorks\libs;
include_once dirname(path: __DIR__).'/init.php';
use NetWorks\libs\Users;
class CSRF{
    private $tokenName = '';
    private $tokenLifetime = 3600; // Token lifetime in seconds (1 hour)
    
    public function __construct(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->tokenName = (new Users())->ip();
    }
    /**
     * Creates a new token
     * @return CSRF
     */
    public function createToken(): static{
        // Start the session if it's not already started
        $this->initializeToken();
        return $this;
    }
    
    // Initialize the CSRF token
    private function initializeToken()
    {
        if (empty($_SESSION[$this->tokenName])) {
            $_SESSION[$this->tokenName] = bin2hex(string: random_bytes(length: 32)); // Generate a new token
            $_SESSION["{$this->tokenName}_time"] = time(); // Store the time of token creation
        }
    }

    // Get the CSRF token for use in forms
    public function getToken(): mixed
    {
        return $_SESSION[$this->tokenName];
    }
    
    // Validate the CSRF token
    public function validateToken($token): bool
    {
        if (empty($_SESSION[$this->tokenName]) || empty($token)) {
            return false; // Token is missing
        }

        if (time() - $_SESSION["{$this->tokenName}_time"] > $this->tokenLifetime) {
            $this->invalidateToken(); // Token expired
            return false;
        }

        if (!hash_equals(known_string: $_SESSION[$this->tokenName], user_string: $token)) {
            return false; // Token does not match
        }

        return true; // Token is valid
    }

    // Invalidate the current CSRF token
    public function invalidateToken(): void
    {
        unset($_SESSION[$this->tokenName]);
        unset($_SESSION["{$this->tokenName}_time"]);
    }
}
?>
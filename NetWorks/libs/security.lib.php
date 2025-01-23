<?php
namespace networks\libs;
use SSQL;
require_once dirname(__DIR__).'/init.php';
class Security{
    private string $security = 'moderate';
    private string $str='';
    private array $allowedURLs = [
        '/https?:\/\/(.*)?google.com(\/.*)?/',
        '/https?:\/\/(.*)?microsoft.com(\/.*)?/',
        '/https?:\/\/(.*)?brave.com(\/.*)?/',
        '/https?:\/\/(.*)?duckduckgo.com(\/.*)?/',
        '/https?:\/\/(.*)?yahoo.com(\/.*)?/',
        '/https?:\/\/(.*)?bing.com(\/.*)?/'
    ];
    /**
     * Loads the security class to networks
     * @param string $str String to check
     */
    public function __construct(string $str) {
        $sql = new SSQL();
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS), true);
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $this->security = $db->selectData('config',['secure'])[0]['secure'];
            }
        }
        $this->str = $str;
    }
    /**
     * Sanitizes any XSS tags
     * @return string sanitize string
     */
    public function XSS(): string{
        return match($this->security){
            'none'=>$this->str,
            'moderate'=>htmlspecialchars($this->str, ENT_QUOTES, 'UTF-8'),
            'strict'=>htmlspecialchars(strip_tags($this->str), ENT_QUOTES, 'UTF-8')
        };
    }
    /**
     * Sanitizes SQL in text
     * @return string sanitized string
     */
    public function sqlInjection(): string{
        return match($this->security){
            'none' => $this->str,
            'moderate' => addslashes($this->str),
            'strict' => addslashes(strip_tags($this->str))
        };
    }
    /**
     * Secures CSRF tokens
     * @return string Secured token
     */
    public function csrf(): string{
        return match($this->security){
            'none' => $this->str,
            'moderate' => hash_hmac('sha256', $this->str, $_SESSION['NETWORKS_KEY']),
            'strict' => hash_hmac('sha256', strip_tags($this->str), $_SESSION['NETWORKS_KEY'])
        };
    }
    /**
     * Logs the data of the security
     * @param int $level Security level
     * @param string $type Security type
     * @param string $ip Request IP
     * @param string $device Requested Device
     * @param string $msg Access method
     * @param array|null $contextData Other context data
     * @return void
     */
    public function log(int $level, string $type, string $ip, string $device, string $msg, array|null $contextData=null): void{
        $slog = fopen(NW_LOGS.NW_DS.'security.log','w+');
        $timestamp = date('Y-m-d H:i:s');
            $contextDataString = $contextData !== null ? json_encode($contextData) : '';

            $logEntry = htmlentities("[{$timestamp}] [{$level}] [{$type}] [Device: {$device}] [IP: {$ip}] - {$msg} {$contextDataString}\n");

        // Write the log entry to the file
        fwrite($slog, $logEntry);
        fclose($slog);
    }
    /**
     * Validates input against a regular expression pattern
     * @param string $pattern Regular expression pattern
     * @return bool True if input matches the pattern, false otherwise
     */
    public function validate(string $pattern): bool {
        return preg_match($pattern, $this->str) === 1;
    }
    /**
     * Encrypts the string using a specified encryption method
     * @param string $method Encryption method (e.g., 'aes-256-cbc')
     * @param string $key Encryption key
     * @return string Encrypted string
     */
    public function encrypt(string $method, string $key): string {
        $ivLength = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($this->str, $method, $key, 0, $iv);
        return base64_encode("{$iv}{$encrypted}");
    }
    /**
     * Decrypts the string using a specified encryption method
     * @param string $method Encryption method (e.g., 'aes-256-cbc')
     * @param string $key Encryption key
     * @return string Decrypted string
     */
    public function decrypt(string $method, string $key): string {
        $data = base64_decode($this->str);
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $method, $key, 0, $iv);
    }
    /**
     * Generates a secure hash of the string
     * @param string $algorithm Hashing algorithm (e.g., 'sha256')
     * @return string Hashed string
     */
    public function hashStr(string $algorithm = 'sha256'): string {
        return hash($algorithm, $this->str);
    }
    /**
     * Add a URL to the allowed list
     * @param string $url URL to allow
     * @return void
     */
    public function allowURL(string $url): void{
        if(!in_array("/https?:\/\/(.*)?$url(\/.*)?/",$this->allowedURLs)) 
            array_push($this->allowedURLs,"/https?:\/\/(.*)?$url(\/.*)?/");
    }
    /**
     * Check if the string is a phishing URL
     * @return bool TRUE if the url is accessible, FALSE if p
     */
    public function phishingCheck(): string{
        foreach($this->allowedURLs as $url){
            if(preg_match($url,$this->str)) 
                $this->str = preg_replace($url,'',$this->str);
        }
        return $this->str;
    }


    /**
     * Triggers all security measures.
     * @return string Secured string
     */
    public function secureAll(): string{
        $this->str = $this->XSS();
        $this->str = $this->sqlInjection();
        $this->str = $this->csrf();
        return $this->str;
    }
}

?>
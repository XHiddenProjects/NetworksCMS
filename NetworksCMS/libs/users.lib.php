<?php
namespace NetWorks\libs;
include_once 'BrowserDetection.php';
use foroco\BrowserDetection;
use NetWorks\libs\Database;
class Users{
    protected BrowserDetection $device;
    public function __construct() {
        $this->device = new BrowserDetection();
    }
    /**
     * Returns the users IP address
     * @return string|bool IP address
     */
    public function ip():string|bool{
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for a proxy IP
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            // If there are multiple IPs, take the first one
            $ipArray = explode(',', $ip);
            $ip = trim($ipArray[0]);
        }
        // Check the remote IP
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if($ip=='::1') $ip = getHostByName(hostname: getHostName());
        if (filter_var(value: $ip, filter: FILTER_VALIDATE_IP)) {
            return $ip;
        } else {
            return false;
        }
    }
    /**
     * Returns the users device
     * @param string $select Information to select, leave empty to select all
     * @see [foroco/php-browser-detection](https://github.com/foroco/php-browser-detection?tab=readme-ov-file#detect-all) to see valid selections
     * @return array|string Array or string of a response
     */
    public function getDevice(string|array $select=''): array|string{
        $finalize = [];
        if(empty($select))
            return $this->device->getAll(ua: $_SERVER['HTTP_USER_AGENT']);
        elseif(is_array(value: $select)){
            foreach($select as $s){
                $finalize[$s] = $this->device->getAll(ua: $_SERVER['HTTP_USER_AGENT'])[$s];
            }
            return $finalize;
        }else
            return $this->device->getAll(ua: $_SERVER['HTTP_USER_AGENT'])[$select];
    }
    /**
     * Returns the current username
     * @return string|null Username, else NULL
     */
    public function get():string|null{
        return $_SESSION['nw_user']??$_COOKIE['nw_user']??null;
    }
    /**
     * Checks if the user is Admin
     * @return bool TRUE if user is admin, else FALSE
     */
    public function isAdmin(): bool{
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $user = $this->get();
        $results = $db->selectTable(name: 'users')->select(conditions:"WHERE username=\"$user\" AND type=\"admin\"");
        $db->close();
        return is_array(value: $results)&&!empty($results) ? true : false;
    }
    /**
     * Checks if the user is Moderator
     * @return bool TRUE if user is moderator, else FALSE
     */
    public function isMod(): bool{
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $user = $this->get();
        $results = $db->selectTable(name: 'users')->select(conditions:"WHERE username=\"$user\" AND type=\"moderator\"");
        $db->close();
        return is_array(value: $results)&&!empty($results) ? true : false;
    }
    /**
     * Checks if user is a Member
     * @return bool TRUE if member, else FALSE
     */
    public function isMember(): bool{
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $user = $this->get();
        $results = $db->selectTable(name: 'users')->select(conditions:"WHERE username=\"$user\" AND type=\"member\"");
        $db->close();
        return is_array(value: $results)&&!empty($results) ? true : false;
    }
}
?>
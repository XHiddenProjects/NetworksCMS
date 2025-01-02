<?php
namespace networks\libs;

use SSQL;
require_once('ssql.lib.php');
require_once(dirname(__DIR__).'/init.php');
/**
 * Users information
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Users{
    protected $conn, $db;
    public function __construct(){
        $this->conn = new SSQL();
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        if($this->conn->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $this->db = $this->conn->selectDB($cred['db']);
        }
    }
    /**
     * Returns the clients IP address
     *
     * @return array users IPV4 data
     */
    public function IP(){
        $status = ['ip'=>'','visibility'=>''];
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        if($ipAddress=='::1'){
            $status['ip'] = getHostByName(getHostName());
            $status['visibility'] = 'private';
        }else{
            $status['ip'] = filter_var($ipAddress,FILTER_FLAG_IPV4);
            $status['visibility'] = 'public';
        }
        return $status;
    }
    /**
     * Generates a unique key
     *
     * @param string $uname Username
     * @param string $ip IPAddress
     * @return string Generated API key
     */
    private function generatePublicKey(string $uname, mixed $ip):string{
        $identifier = $uname.$ip;
        $randBytes = random_bytes(32);
        $hash = hash('sha256',$identifier.$randBytes);
        return substr($hash,random_int(0,strlen($hash)-(floor(strlen($hash)/2))));
    }
    /**
     * Add a user
     *
     * @param string $username Username
     * @param string $email
     * @param string $password
     * @param string $first_name First name
     * @param string $m_int Middle inital
     * @param string $last_name Last name
     * @param string $perm permission
     * @return bool
     */
    public function add(string $username, string $email, string $password, string $first_name, string $m_int, string $last_name, string $perm):bool{
        if(!$this->db->selectData('users',['*'],'WHERE username="'.$username.'"')){
            $this->db->addData('users',
            ['username','email','pass','fname','mname','lname','permission','ip','public_key'],
        [htmlentities($username),filter_var($email,FILTER_SANITIZE_EMAIL),password_hash($password,PASSWORD_DEFAULT),htmlentities($first_name),htmlentities($m_int),htmlentities($last_name),htmlentities($perm),$this->IP()['ip'],$this->generatePublicKey(htmlentities($username),$this->IP()['ip'])]);
            $this->conn->close();
            return true;
        }else{
            $this->conn->close();
            return false;
        } 
    }
    /**
     * Checks if user is online
     * @param string $username
     * @return bool
     */
    public function isOnline(string $username):bool{
        return strtotime($this->db->selectData('users',['*'],'WHERE username="'.$username.'"')[0]['OnlineStat'])+5 > time();
    }
}
?>
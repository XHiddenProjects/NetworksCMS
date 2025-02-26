<?php
use NetWorks\libs\Database;
header(header: 'Content-Type: application/json; charset=utf-8');
include_once dirname(path: __DIR__).'/init.php';
use PHPMailer\PHPMailer\PHPMailer;
use NetWorks\libs\Utils;
use NetWorks\libs\CSRF;
use NetWorks\libs\Users;
$utils = new Utils();
$csrf = new CSRF();
$user = new Users();
global $lang;
if($utils->isREQUEST(element: 'token'))
    echo json_encode(value: ['token_valid'=>$csrf->validateToken(token: htmlspecialchars(string: $_REQUEST['token']))],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
if($utils->isREQUEST(element: 'check')){
    $username = htmlspecialchars(string: $_REQUEST['username']);
    $email = filter_var(value: filter_var(value: $_REQUEST['email'], filter: FILTER_VALIDATE_EMAIL),filter: FILTER_SANITIZE_EMAIL);
    $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
    $results = $db->selectTable(name: 'users')->select(conditions: "username=\"$username\" OR email=\"$email\"", mode: Database::ASSOC);
    echo json_encode(value: ['canPass'=>(empty($results) ? true : false)],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    $db->close();
}
if($utils->isREQUEST(element: 'signup')){
    $fname = htmlspecialchars(string: $_REQUEST['fname']);
    $mname = htmlspecialchars(string: $_REQUEST['mname']);
    $lname = htmlspecialchars(string: $_REQUEST['lname']);
    $username = htmlspecialchars(string: $_REQUEST['username']);
    $email = filter_var(value: $_REQUEST['email'],filter: FILTER_SANITIZE_EMAIL);
    $psw = password_hash(password: $_POST['psw'],algo: PASSWORD_DEFAULT);
    $db = new Database(file: 'NetworkCMS',flags: Database::OPEN_READWRITE);
    $table = $db->selectTable(name: 'users');
    $insertSQL = $table->insert(data:[
        'fname'=>$fname,
        'mname'=>$mname,
        'lname'=>$lname,
        'email'=>$email,
        'psw'=>$psw,
        'ip'=>$user->ip(),
        'browser'=>$user->getDevice()['browser_title'],
        'os'=>$user->getDevice()['os_title'],
        'type'=>'member'
    ]);
    if($insertSQL){
        $_SESSION['nw_user'] = $username;
        echo json_encode(value: ['created'=>1],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }else
        echo json_encode(value: ['err'=>1],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}
?>
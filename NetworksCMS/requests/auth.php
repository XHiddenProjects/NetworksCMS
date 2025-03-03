<?php
use NetWorks\libs\Database;
header(header: 'Content-Type: application/json; charset=utf-8');
include_once dirname(path: __DIR__).'/init.php';
use PHPMailer\PHPMailer\PHPMailer;
use NetWorks\libs\Utils;
use NetWorks\libs\CSRF;
use NetWorks\libs\Users;
use NetWorks\libs\Storage;
$utils = new Utils();
$csrf = new CSRF();
$user = new Users();
$storage = new Storage();
global $lang;
if($utils->isREQUEST(element: 'check')){
    $username = htmlspecialchars(string: $_REQUEST['username']);
    $email = filter_var(value: filter_var(value: $_REQUEST['email'], filter: FILTER_VALIDATE_EMAIL),filter: FILTER_SANITIZE_EMAIL);
    $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
    $results = $db->selectTable(name: 'users')->select(conditions: "WHERE username=\"$username\" OR email=\"$email\"");
    echo json_encode(value: ['canPass'=>(empty($results) ? true : false)],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    $db->close();
}
global $lang;
if($utils->isREQUEST(element: 'signup')){
    if(!$csrf->validateToken(token: htmlspecialchars(string: $_REQUEST['token']))){
        echo json_encode(value: ['token_valid'=>false],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }else{
        $fname = htmlspecialchars(string: $_REQUEST['fname']);
        $mname = htmlspecialchars(string: $_REQUEST['mname']);
        $lname = htmlspecialchars(string: $_REQUEST['lname']);
        $username = htmlspecialchars(string: $_REQUEST['username']);
        $email = filter_var(value: $_REQUEST['email'],filter: FILTER_SANITIZE_EMAIL);
        $psw = password_hash(password: $_REQUEST['psw'],algo: PASSWORD_DEFAULT);
        $db = new Database(file: 'NetworksCMS',flags: Database::OPEN_READWRITE);
        $table = $db->selectTable(name: 'users');
        $admin = $table->select(conditions:'WHERE type="admin"');
        $confirmed = $utils->sendMail(from: [$admin['email']=>"{$admin['fname']} {$admin['lname']}"],
        to: [$email=>"$fname $lname"],
        subject: $lang['email_confirm_user_subject'],
        body: $lang['email_confirm_user_body']) ? 0 : 1;
        $results = $db->selectTable(name: 'users')->select(conditions: "WHERE username=\"$username\" OR email=\"$email\"");
        if(!empty($results)){
            echo json_encode(value: ['created'=>false],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        }else{
            $insertSQL = $table->insert(name:null,data:[
                'fname'=>$fname,
                'mname'=>$mname,
                'lname'=>$lname,
                'username'=>$username,
                'email'=>$email,
                'psw'=>$psw,
                'ip'=>$user->ip(),
                'browser'=>$user->getDevice()['browser_title'],
                'os'=>$user->getDevice()['os_title'],
                'type'=>'member',
                'confirmed'=>$confirmed
            ]);
            $db->selectTable(name: 'online')->insert(name: null,data: [
                'username'=>$username,
                'status'=>1
            ]);
            $db->close();
            if($insertSQL){
                $_SESSION['nw_user'] = $username;
                echo json_encode(value: ['created'=>true],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            }else
                echo json_encode(value: ['created'=>false],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        }
    }
}
if($utils->isREQUEST(element: 'online')){
    if(file_exists(filename: NW_DATABASE.NW_DS.'NetworksCMS.db')){
        $db = new Database(file: 'NetworksCMS',flags: Database::OPEN_READWRITE);
        $online = $db->selectTable(name: 'online');
        if($user->get()){
            $online->update(data: [
                'last_checked'=>date(format: 'Y-m-d H:i:s')
            ],conditions: 'username="'.$user->get().'"');
        }
        $select = $online->select(selector: '*',ignoreAuto: true);
            for($i=0;$i<count(value: $select);$i++){
                if(strtotime(datetime: $select[$i]['last_checked'])+1>strtotime(datetime: date(format: 'Y-m-d H:i:s'))){
                    $online->update(data:[
                        'status'=>1
                    ],conditions:'username="'.$select[$i]['username'].'"');
                }else{
                    $online->update(data:[
                        'status'=>0
                    ],name: null, conditions:'username="'.$select[$i]['username'].'"');
                }
            }
        $target = $online->select(selector:'*');
        $db->close();
    }
}
if($utils->isREQUEST(element: 'logout')){
    $db = new Database(file: 'NetworksCMS',flags: Database::OPEN_READWRITE);
    $db->selectTable(name: 'online')->update(data:[
        'status'=>0
    ],name:null,conditions: 'username="'.$user->get().'"');
    $db->close();
    $storage->deleteCookie(name: session_name());
    $storage->deleteSession();
    echo json_encode(value: ['loggedOut'=>true],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}
if($utils->isREQUEST(element: 'login')){
    if(!$csrf->validateToken(token: htmlspecialchars(string: $_REQUEST['token']))){
        echo json_encode(value: ['token_valid'=>false],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }else{
        $auth = htmlspecialchars(string: $_REQUEST['auth']);
        $psw = $_REQUEST['psw'];
        $remember = filter_var(value: $_REQUEST['remember'],filter: FILTER_VALIDATE_BOOLEAN);
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $pass = true;
        $select = $db->selectTable(name: 'users')->select(conditions: "WHERE username='$auth' OR email='$auth'");
        if(empty($select)) $pass = false;
        else{
            if(!password_verify(password: $psw,hash: $select['psw'])) $pass=false;
        }

        if($pass){
            $remember ? $storage->setCookie(name: 'nw_user', value: $select['username'], time: 30, path: '/') : $storage->setSession(name: 'nw_user',value: $select['username']);
        }

        echo json_encode(value: ['login'=>$pass],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
    
}
?>
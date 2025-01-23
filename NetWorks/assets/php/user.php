<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
use networks\libs\Users;
use networks\libs\Utils;

require_once dirname(__DIR__,2).'/init.php';

if(file_exists(NW_SQL_CREDENTIALS)){

$cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);

$sql = new SSQL();
$user = new Users();

if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
    if($sql->checkDB($cred['db'])){
        if(isset($_REQUEST['action'])){
            if(strtolower($_REQUEST['action'])==='add'){
                if(!($sql->selectDB($cred['db']))->selectData('users',['*'],'WHERE username="'.htmlentities($_REQUEST['username']).'" OR email="'.htmlentities($_REQUEST['email']).'"')){
                    if(preg_match('/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})/',$_REQUEST['psw'])){
                        if($_REQUEST['psw']===$_REQUEST['cpsw']){
                            if($user->add($_REQUEST['username'],$_REQUEST['email'],$_REQUEST['psw'],$_REQUEST['fname'],$_REQUEST['mint'],$_REQUEST['lname'],$_REQUEST['perm'])){
                                $db = $sql->selectDB($cred['db']);
                                $db->addData('logger',['eventType','ip','eventDescription','eventStatus'], ['signup',(new Users())->IP()['ip'],htmlentities($_REQUEST['username']),'success']);
                                echo json_encode(['success'=>true],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(['err'=>(new Lang())->get('Errors','userAdd')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            }
                            }else echo json_encode(['err'=>(new Lang())->get('Errors','passwordMatch')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                    }else
                        echo json_encode(['err'=>(new Lang())->get('Errors','passwordRequirements')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }else 
                    echo json_encode(['err'=>(new Lang())->get('Errors','usernameOrEmailExists')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            }
            elseif(strtolower($_REQUEST['action'])==='get'){
                switch(strtolower($_REQUEST['type'])){
                    case 'ip':
                        echo json_encode(['success'=>$user->IP()['ip']],JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
                    break;
                }
            }
            elseif(strtolower($_REQUEST['action'])==='login'){
                $db = $sql->selectDB($cred['db']);
                $userData = $db->selectData('users',['*'],'WHERE username="'.htmlentities($_REQUEST['username']).'" OR email="'.filter_var(htmlentities($_REQUEST['username']),FILTER_VALIDATE_EMAIL).'"');
                if(!empty($userData)){
                    if(password_verify($_REQUEST['psw'],$userData[0]['pass'])){
                        if(!isset($_COOKIE['user'])){
                            setcookie('user',$userData[0]['username'],time() + (86400 * 30),'/');
                            echo json_encode(['success'=>1],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            $db->addData('logger',['eventType','ip','eventDescription','eventStatus'], ['login',(new Users())->IP()['ip'],htmlentities($_REQUEST['username']),'success']);
                        }else echo json_encode(['success'=>1],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                    }else {
                        $db->addData('logger',['eventType','ip','eventDescription','eventStatus'], ['login',(new Users())->IP()['ip'],htmlentities($_REQUEST['username']),'failure']);
                        echo json_encode(['err'=>(new Lang())->get('Errors','invalidPsw')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                    }
                }else {
                    $db->addData('logger',['eventType','ip','eventDescription','eventStatus'], ['login',(new Users())->IP()['ip'],htmlentities($_REQUEST['username']),'failure']);
                    echo json_encode(['err'=>(new Lang())->get('Errors','noUsernameOrEmailExists')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }
            }elseif(strtolower($_REQUEST['action'])==='logout'){
                $db = $sql->selectDB($cred['db']);
                if(isset($_COOKIE['user'])){
                    $db->updateData('users','isOnline="0"','username="'.$_COOKIE['user'].'"');
                    setcookie('user','',time()-3600,'/');
                    $db->addData('logger',['eventType','ip','eventDescription','eventStatus'], ['logout',(new Users())->IP()['ip'],htmlentities($_COOKIE['user']),'success']);
                    echo json_encode(['success'=>1],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }
            }elseif(strtolower($_REQUEST['action'])==='online'){
                $db = $sql->selectDB($cred['db']);
                if(isset($_COOKIE['user'])){
                    if($db->updateData('users','OnlineStat="'.date('Y-m-d H:i:s').'"','username="'.$_COOKIE['user'].'"')&&
                    $db->updateData('users','isOnline="1"','username="'.$_COOKIE['user'].'"'))
                        echo json_encode(['success'=>1],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }else{
                    $selectAll = $db->selectData('users',['username']);
                    if(is_array($selectAll)){
                        foreach($selectAll as $user){
                            if(!(new Users())->isOnline($user['username'])){
                                if($db->updateData('users','isOnline="0"','username="'.$user['username'].'"'));
                            }
                        }
                    }
                }
            }
        }
    }else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLDB')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLCred')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}else echo json_encode(['err'=>''],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>
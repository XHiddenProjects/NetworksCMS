<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
use networks\libs\Users;
require_once(dirname(__DIR__,2).'/libs/ssql.lib.php');
require_once(dirname(__DIR__,2).'/libs/lang.lib.php');
require_once(dirname(__DIR__,2).'/init.php');
require_once(dirname(__DIR__,2).'/libs/users.lib.php');

$cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);

$sql = new SSQL();
$user = new Users();

if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
    if($sql->checkDB($cred['db'])){
        if(isset($_REQUEST['action'])){
            if(strtolower($_REQUEST['action'])==='add'){
                if(!($sql->selectDB($cred['db']))->selectData('users',['*'],'WHERE username="'.htmlentities($_REQUEST['username']).'"')){
                    if(preg_match('/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})/',$_REQUEST['psw'])){
                        if($_REQUEST['psw']===$_REQUEST['cpsw']){
                            if($user->add($_REQUEST['username'],$_REQUEST['email'],$_REQUEST['psw'],$_REQUEST['fname'],$_REQUEST['mint'],$_REQUEST['lname'],$_REQUEST['perm']))
                                echo json_encode(['success'=>true],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            else
                                echo json_encode(['err'=>(new Lang())->get('Errors','userAdd')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                            }else echo json_encode(['err'=>(new Lang())->get('Errors','passwordMatch')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                    }else
                        echo json_encode(['err'=>(new Lang())->get('Errors','passwordRequirements')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }else json_encode(['err'=>(new Lang())->get('Errors','usernameExists')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                $sql->close();
            }
        }
    }else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLDB')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLCred')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

?>
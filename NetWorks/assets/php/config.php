<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
require_once(dirname(__DIR__,2).'/init.php');
if(file_exists(NW_SQL_CREDENTIALS)){
    $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
    $sql = new SSQL();

    if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
        if($sql->checkDB($cred['db'])){
            if(isset($_REQUEST['type'])){
                if(strtolower($_REQUEST['type'])==='recaptcha'){
                    $skey = $sql->selectData('recaptcha',[htmlentities($_REQUEST['value'])])[0][htmlentities($_REQUEST['value'])];
                    echo json_encode(['success'=>$skey]);
                }else{
                    $config = $sql->selectData('config',[htmlentities($_REQUEST['value'])])[0][htmlentities($_REQUEST['value'])];
                    echo json_encode(['success'=>$config]);
                }
                
            }
        }else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLDB')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }else echo json_encode(['err'=>(new Lang())->get('Errors','noSQLCred')],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}else echo json_encode(['success'=>'en-us'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>
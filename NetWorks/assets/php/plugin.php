<?php
header('Content-Type: application/json');
require_once dirname(__DIR__,2).'/init.php';
if(isset($_REQUEST['name'])){
    $sql = new SSQL();
    if(file_exists(NW_SQL_CREDENTIALS)){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS), true);
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            if(isset($_REQUEST['status'])){
                if($_REQUEST['status']){
                    if($db->updateData('plugins',"pluginStatus=1",'pluginName="'.htmlentities($_REQUEST['name']).'" AND pluginDisabled=0'))
                        echo json_encode(['success'=>1]);
                    else echo json_encode(['success'=>0]);
                }else {
                    if($db->updateData('plugins',"pluginStatus=0",'pluginName="'.htmlentities($_REQUEST['name']).'" AND pluginDisabled=0'))
                        echo json_encode(['success'=> 1]);
                    else echo json_encode(['success'=>0]);
                }
            }else echo json_encode(['err'=> 'status not found']);
        }else echo json_encode(['err'=> 'no credentials found']);
    }
}
?>
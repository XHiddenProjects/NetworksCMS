<?php
use networks\libs\Web;
include_once "init.php";
$query = (new Web())->getQuery();
if($query['user']===$_COOKIE['user']){
    if(file_exists(NW_SQL_CREDENTIALS)){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $checkMember = $db->selectData('users',['*'],'WHERE username="'.$query['user'].'" AND NOT permission="guest"');
            if(empty($checkMember)){
                if($db->updateData('users','permission="member"','username="'.$query['user'].'"'))
                    echo '<script>window.open("./dashboard","_self")</script>';
            }else echo '<script>window.open("./dashboard","_self")</script>';
        }
    }
}
?>
<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
require_once dirname(__DIR__,2).'/init.php';
$cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
$sql = new SSQL();
if(isset($_REQUEST['get'])){
    switch(strtolower($_REQUEST['get'])){
        case '':

        break;
    }
}
?>
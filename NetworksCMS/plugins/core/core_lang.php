<?php
header(header: 'Content-Type: application/json; charset=utf-8');
include_once dirname(path: __DIR__,levels: 2).'/init.php';
use NetWorks\libs\Utils;
use NetWorks\libs\Users;
$utils = new Utils();
$user = new Users();
echo json_encode(value: ['dictionary'=>$utils->getDictionary(),'user'=>$user->get()],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
?>
<?php
include_once 'init.php';
include_once 'header.php';
include_once 'footer.php';
use NetWorks\libs\Templates;
use NetWorks\libs\Users;
$user = new Users();
if(!$user->get()) header(header: 'Location: '.NW_DOMAIN."/auth/login");
$template = new Templates();
echo "$header
".$template->load(name: 'dashboard').
$footer;
?>
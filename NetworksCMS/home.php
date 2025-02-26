<?php
include_once 'init.php';
include_once 'header.php';
include_once 'footer.php';
use NetWorks\libs\Templates;
$template = new Templates();
echo "$header
".$template->load(name: 'home').
$footer;
?>
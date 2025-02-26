<?php
include_once 'init.php';
include_once 'header.php';
include_once 'footer.php';
use NetWorks\libs\Templates;

$paths = NW_PATH_ARRAY;
$template = new Templates();
echo $header.
$template->load(name: end(array: $paths)).
$footer;
?>
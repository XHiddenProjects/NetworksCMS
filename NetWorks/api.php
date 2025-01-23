<?php
header('Content-Type: application/json');
use networks\libs\Web;
include_once 'init.php';
include_once 'api/api.php';
$web = new Web();
$search = array_search('api', $web->getPath());
# Gets the receiving API
$target = array_slice($web->getPath(), $search+1);
# Get Queries
$query = $web->getQuery();
// Render API
echo api($target[0],$target[1]??'',$query);
?>
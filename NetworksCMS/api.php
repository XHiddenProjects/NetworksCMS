<?php
include_once 'init.php';
use NetWorks\api\ForumController;
use NetWorks\api\RepliesController;
use NetWorks\api\TopicsController;
use NetWorks\api\UserController;
$uri = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);
$uri = explode( separator: '/', string: $uri );
$uri[2] = $uri[count(value: $uri)-2];
if ((isset($uri[2]) && $uri[2] != 'user'&&$uri[2]!=='forums'&&$uri[2]!=='replies'&&$uri[2]!=='topics') || !isset($uri[3])) {
    header(header: "HTTP/1.1 404 Not Found");
    exit();
}
$uri[3] = end(array: $uri);
if($uri[2]==='user'){
    $objFeedController = new UserController();
    $strMethodName = "$uri[3]Action";
    $objFeedController->{$strMethodName}();
}
if($uri[2]==='forums'){
    $objFeedController = new ForumController();
    $strMethodName = "$uri[3]Action";
    $objFeedController->{$strMethodName}();
}
if($uri[2]==='replies'){
    $objFeedController = new RepliesController();
    $strMethodName = "$uri[3]Action";
    $objFeedController->{$strMethodName}();
}
if($uri[2]==='topics'){
    $objFeedController = new TopicsController();
    $strMethodName = "$uri[3]Action";
    $objFeedController->{$strMethodName}();
}
?>
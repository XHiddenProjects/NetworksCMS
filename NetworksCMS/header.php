<?php
if((function_exists(function: 'session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id())
    session_start();
use NetWorks\libs\Plugins;
use NetWorks\libs\Database;
use NetWorks\libs\Files;
include_once 'init.php';
global $lang;
$f = new Files();
$plugins = new Plugins();
if($f->exists(path: NW_DATABASE.NW_DS.'NetworksCMS.db')){
    $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
    $results = $db->selectTable(name: 'settings')->select(mode: Database::ASSOC);
    $db->close();
}else $results = [];
if(!$f->exists(path: 'installed.bin.key')&&!preg_match(pattern: '/install/',subject: NW_PATH)) header(header: 'Location: '.NW_DOMAIN.'/install');
$end = NW_PATH_ARRAY;
$title = preg_replace(pattern: '/\?.*$/',replacement: '',subject: strtolower(string: end(array: $end)??NW_PATH_ARRAY[0]));
$title = in_array(needle: 'dashboard',haystack: NW_PATH_ARRAY) ? 'dashboard' : $title;
if(!isset(NW_PATH_ARRAY[1])||empty(NW_PATH_ARRAY)) $title = 'home';
$header = '<!DOCTYPE html>
<html lang="'.$lang['abbr'].'">
<head>
    <meta charset="'.($results['charset']??CHARSET).'">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="'.($results['author']??$lang['author']).'"/>
    <meta name="description" content="'.($results['description']??$lang['description']).'"/>
    <title>'.$lang[$title].' - '.($results['title']??$lang['projectName']).'</title>
    <link rel="icon" type="image/x-icon" href="'.NW_THEMES.NW_DS.'default'.NW_DS.'images'.NW_DS.'favicon.ico">
    '.$plugins->hook(hookName: 'head').'
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Material+Symbols+Outlined|Material+Symbols+Sharp|Material+Symbols+Rounded" rel="stylesheet"/>
    '.(file_exists(filename: NW_ROOT.NW_DS.'themes'.NW_DS.($results['theme']??'default').NW_DS.'css'.NW_DS.$title.'.css') ? '<link rel="stylesheet" href="'.NW_THEMES.NW_DS.($results['theme']??'default').NW_DS.'css'.NW_DS.$title.'.css"/>' : '') .'
    <link rel="stylesheet" href="'.NW_THEMES.NW_DS.($results['theme']??'default').NW_DS.'css'.NW_DS.'mobile.css"/>
    <link rel="stylesheet" href="'.NW_THEMES.NW_DS.($results['theme']??'default').NW_DS.'css'.NW_DS.'footer.css"/>
    '.$plugins->hook(hookName: 'css').'
</head>
<body>'.$plugins->hook(hookName: 'beforeLoad');
?>
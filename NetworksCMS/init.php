<?php

use NetWorks\libs\Files;
include_once 'autoloader.php';
$p = preg_split(pattern: '/\//',subject: dirname(path: __FILE__));
$folder = end(array: $p);
$domain = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on' ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].($p==='htdocs' ? '' : "/$folder");
$path = preg_replace(pattern: '/\/?/',replacement: '',subject: preg_replace(pattern: "/$folder\//",replacement: '',subject: $_SERVER['REQUEST_URI']));
!defined(constant_name: 'NW_ROOT') ? define(constant_name: 'NW_ROOT',value: dirname(path: __FILE__)) : '';
!defined(constant_name: 'NW_DOMAIN') ? define(constant_name: 'NW_DOMAIN',value: $domain) : '';
!defined(constant_name: 'NW_DS') ? define(constant_name: 'NW_DS',value: '/') : '';
!defined(constant_name: 'NW_PATH') ? define(constant_name: 'NW_PATH',value: "$domain/$path") : '';
!defined(constant_name: 'NW_PATH_ARRAY') ? define(constant_name: 'NW_PATH_ARRAY',value:  array_values(array_filter(preg_split('/\//',$_SERVER['REQUEST_URI']),function($e){return $e!=='';}))) : '';
!defined(constant_name: 'NW_PLUGINS_DIR') ? define(constant_name: 'NW_PLUGINS_DIR',value: NW_ROOT.NW_DS.'plugins') : '';
!defined(constant_name: 'NW_PLUGIN') ? define(constant_name: 'NW_PLUGIN',value: "$domain/plugins") : '';
!defined(constant_name: 'NW_THEMES_DIR') ? define(constant_name: 'NW_THEMES_DIR',value: NW_ROOT.NW_DS.'themes') : '';
!defined(constant_name: 'NW_THEMES') ? define(constant_name: 'NW_THEMES',value: "$domain/themes") : '';
!defined(constant_name: 'NW_LANGUAGES') ? define(constant_name: 'NW_LANGUAGES',value: NW_ROOT.NW_DS.'languages') : '';
!defined(constant_name: 'NW_DATABASE') ? define(constant_name: 'NW_DATABASE',value: NW_ROOT.NW_DS.'databases') : '';
!defined(constant_name: 'NW_UPLOADS_DIR') ? define(constant_name: 'NW_UPLOADS_DIR',value: NW_ROOT.NW_DS.'uploads') : '';
!defined(constant_name: 'NW_API') ? define(constant_name: 'NW_API',value: "$domain/api") : '';
!defined(constant_name: 'NW_UPLOADS') ? define(constant_name: 'NW_UPLOADS',value: "$domain/uploads") : '';
!defined(constant_name: 'CHARSET') ? define(constant_name: 'CHARSET',value: 'utf-8') : '';
!defined(constant_name: 'NW_TEMPLATES') ? define(constant_name: 'NW_TEMPLATES',value: NW_ROOT.NW_DS.'templates') : '';

# languages
$f = new Files();
foreach(array_diff(scandir(directory: dirname(path: __FILE__).'/languages'),['.','..'])  as $langs){
    if($langs==='en-us.php'){
        include_once NW_LANGUAGES.NW_DS.$langs;
        foreach($f->scan(dir: NW_PLUGINS_DIR) as $plugin){
            if($f->exists(path: dirname(path: __FILE__)."/plugins/$plugin/lang/$langs"))
                include_once dirname(path: __FILE__)."/plugins/$plugin/lang/$langs";
        }
    }
}
if(!file_exists(filename: NW_UPLOADS_DIR)) mkdir(directory: NW_UPLOADS_DIR);
$GLOBALS['lang'] = $lang;
?>
<?php
namespace networks;

use networks\Exception\FileHandlingException;
use networks\libs\Lang;
use networks\libs\Plugins;
use SSQL;

require_once 'autoloader.php';

$lang = new Lang();
$sql = new SSQL();
(!defined('NW_DS') ? define('NW_DS','/') : '');
(!defined('NW_ROOT') ? define('NW_ROOT',dirname(__FILE__)) : '');
(!defined('NW_TEMPLATES') ? define('NW_TEMPLATES',dirname(__FILE__).NW_DS.'template') : '');
(!defined('NW_PLUGINS') ? define('NW_PLUGINS',dirname(__FILE__).NW_DS.'plugins') : '');
(!defined('NW_THEMES') ? define('NW_THEMES',dirname(__FILE__).NW_DS.'themes') : '');
(!defined('NW_ASSETS') ? define('NW_ASSETS',dirname(__FILE__).NW_DS.'assets') : '');
(!defined('NW_LANG') ? define('NW_LANG',dirname(__FILE__).NW_DS.'languages') : '');
(!defined('NW_DRAFTS') ? define('NW_DRAFTS',dirname(__FILE__).NW_DS.'drafts') : '');
(!defined('NW_UPLOADS') ? define('NW_UPLOADS',dirname(__FILE__).NW_DS.'uploads') : '');
(!defined('NW_SQL_CREDENTIALS') ? define('NW_SQL_CREDENTIALS',NW_ASSETS.NW_DS.'sql'.NW_DS.'credentials.json') : '');
(!defined('NW_CHARSET') ? define('NW_CHARSET','UTF-8') : '');
(!defined('NW_LOGS') ? define('NW_LOGS',dirname(__FILE__).NW_DS.'logs') : '');

if(!file_exists(NW_LOGS)) mkdir('logs');

foreach(array_diff(scandir(NW_PLUGINS),['.','..']) as $plugins){
    if(file_exists(NW_SQL_CREDENTIALS)){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw']))
            $db = $sql->selectDB($cred['db']);
        try{
            if(file_exists(NW_PLUGINS.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php')){
                require_once NW_PLUGINS.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php';
                if(empty($db->selectData('plugins',['*'],"WHERE pluginName=\"{$plugins}\""))){
                    (new $plugins());
                }
            }
            else throw new FileHandlingException($lang->get('Errors','noFile'),'plugins'.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php');
        }catch(FileHandlingException $e){
            echo '<b>NetWorks File_Handling:</b> '.$e->getMessage().' <em>'.$e->getPath().'</em> on line '.$e->getLine();
        }
    }
    
}
# Load non-loaded folders
if(!file_exists(NW_DRAFTS)) mkdir(NW_DRAFTS);
if(!file_exists(NW_UPLOADS)) mkdir(NW_UPLOADS);

# Set Timezone
if(file_exists(NW_SQL_CREDENTIALS)){
    $sql = new SSQL();
    $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
    if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
        $db = $sql->selectDB($cred['db']);
        $tz = $db->selectData('config',['timezone'])[0]['timezone'];
        date_default_timezone_set($tz);
        $sql->close();
    }
}else{
    date_default_timezone_set('Europe/Berlin');
}

# Update htdocs
$access = file_get_contents(dirname(__FILE__).'/.htaccess');
$root = $_SERVER['DOCUMENT_ROOT'];
$currPath = array_values(array_filter(explode('/',$_SERVER['REQUEST_URI']),function($e){
    return $e!=='';
}));

if(preg_replace('/\\\\/','/',dirname(__FILE__))!==$_SERVER['DOCUMENT_ROOT']){
    preg_match_all('/\/\/((.*?)\/)?errors\/[\d]{3}/',$access,$matches);
    foreach($matches[0] as $match){
        if(!preg_match("/\/\/{$currPath[0]}/",$access)){
            if(!preg_match('/\/\/errors/',$match)){
                $match = preg_replace_callback('/\/\/(.*?)\/errors/',function(){
                    return '//errors';
                },$match);
            }
            $access = str_replace($match,'//'.$currPath[0].str_replace('//','/',$match),$access);
        }
    }
    file_put_contents(dirname(__FILE__).'/.htaccess',$access);
}else{
    preg_match_all('/\/\/(.*?)\/errors\/[\d]{3}/',$access,$matches);
    foreach($matches[0] as $match){
        if (preg_match('/\/errors\/([\d]{3})/', $match, $errorMatch)) {
            $access = '//'.str_replace($match, $errorMatch[0], $access);
        }
    }
    file_put_contents(dirname(__FILE__).'/.htaccess',$access);
}

# Update nginx.conf
$access = file_get_contents(dirname(__FILE__).'/nginx.conf');
$root = $_SERVER['DOCUMENT_ROOT'];
$currPath = array_values(array_filter(explode('/',$_SERVER['REQUEST_URI']),function($e){
    return $e!=='';
}));

if(preg_replace('/\\\\/','/',dirname(__FILE__))!==$_SERVER['DOCUMENT_ROOT']){
    preg_match_all('/\/\/((.*?)\/)?errors\/[\d]{3}/',$access,$matches);
    foreach($matches[0] as $match){
        if(!preg_match("/\/\/{$currPath[0]}/",$access)){
            
            if(!preg_match('/\/\/errors/',$match)){
                $match = preg_replace_callback('/\/\/(.*?)\/errors/',function(){
                    return '//errors';
                },$match);
            }
            $access = str_replace($match,'//'.$currPath[0].str_replace('//','/',$match),$access);
        }
    }
    file_put_contents(dirname(__FILE__).'/nginx.conf',$access);
}else{
    preg_match_all('/\/\/(.*?)\/errors\/[\d]{3}/',$access,$matches);
    foreach($matches[0] as $match){
        if (preg_match('/\/errors\/([\d]{3})/', $match, $errorMatch)) {
            $access = '//'.str_replace($match, $errorMatch[0], $access);
        }
    }
    file_put_contents(dirname(__FILE__).'/nginx.conf',$access);
}

if(!file_exists(NW_UPLOADS.NW_DS.'profile')) mkdir(NW_UPLOADS.NW_DS.'profile');

?>
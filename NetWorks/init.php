<?php
namespace networks;

use networks\Exception\FileHandlingException;
use networks\libs\Lang;
use SSQL;

require_once('libs/lang.lib.php');
require_once('libs/ssql.lib.php');


$lang = new Lang();

(!defined('NW_DS') ? define('NW_DS','/') : '');
(!defined('NW_ROOT') ? define('NW_ROOT',dirname(__FILE__)) : '');
(!defined('NW_TEMPLATES') ? define('NW_TEMPLATES',dirname(__FILE__).NW_DS.'template') : '');
(!defined('NW_PLUGINS') ? define('NW_PLUGINS',dirname(__FILE__).NW_DS.'plugins') : '');
(!defined('NW_THEMES') ? define('NW_THEMES',dirname(__FILE__).NW_DS.'themes') : '');
(!defined('NW_ASSETS') ? define('NW_ASSETS',dirname(__FILE__).NW_DS.'assets') : '');
(!defined('NW_LANG') ? define('NW_LANG',dirname(__FILE__).NW_DS.'languages') : '');
(!defined('NW_DRAFTS') ? define('NW_DRAFTS',dirname(__FILE__).NW_DS.'drafts') : '');
(!defined('NW_SQL_CREDENTIALS') ? define('NW_SQL_CREDENTIALS',NW_ASSETS.NW_DS.'sql'.NW_DS.'credentals.json') : '');
foreach(array_diff(scandir(NW_PLUGINS),['.','..']) as $plugins){
    try{
        if(file_exists(NW_PLUGINS.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php'))
            require_once(NW_PLUGINS.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php');
        else throw new FileHandlingException($lang->get('Errors','noFile'),'plugins'.NW_DS.$plugins.NW_DS.$plugins.'.nwplg.php');
    }catch(FileHandlingException $e){
        echo '<b>NetWorks File_Handling:</b> '.$e->getMessage().' <em>'.$e->getPath().'</em> on line '.$e->getLine();
    }
}

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
        if(!preg_match('/\/\/'.$currPath[0].'/',$access)){
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
        $access = '//'.str_replace($match,preg_match('/\/errors\/([\d]{3})/',$match)[0],$access);
    }
    file_put_contents(dirname(__FILE__).'/.htaccess',$access);
}
?>
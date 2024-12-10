<?php
namespace networks\libs;
use networks\libs\Users;
use networks\libs\Lang;
use networks\libs\Plugins;
use networks\libs\Web;
use SSQL;

require_once('users.lib.php');
require_once('lang.lib.php');
require_once('plugins.lib.php');
require_once('web.lib.php');
require_once('ssql.lib.php');
require_once(dirname(__DIR__).'/init.php');

(!defined('NW_DICTIONARY_USER') ? define('NW_DICTIONARY_DEFAULT',array(
    '%USER_LANGUAGE%'=>function(){return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);},
    '%USER_DATETIME%'=>function(){return date('Y-m-d H:i:s');},
    '%USER_DATE%'=>function(){return date('Y-m-d');},
    '%USER_TIME%'=>function(){return date('H:i:s');},
    '%USER_IP%'=>function(){return (new Users())->IP()['ip'];},
    '%USER_IP_VISIBILITY%'=>function(){return (new Users())->IP()['visibility'];}
)) : '');
(!defined('NW_DICTIONARY_META') ? define('NW_DICTIONARY_META',array(
    '%META_CHARSET=(.+?)%'=>function($e){return '<meta charset="'.$e[1].'"/>';},
    '%META_DESCRIPTION=(.+)%'=>function($e){return '<meta name="description" content="'.$e[1].'"/>';},
    '%META_VIEWPORT%'=>function(){return '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';},
    '%META_AUTHOR=(.+)%'=>function($e){return '<meta name="author" content="'.$e[1].'"/>';},
    '%META_TWITTER_CARD=(.+)%'=>function($e){return '<meta name="twitter:card" content="'.$e[1].'"/>';},
    '%META_TWITTER_TITLE=(.+)%'=>function($e){return '<meta name="twitter:title" content="'.$e[1].'"/>';},
    '%META_TWITTER_DESCRIPTION=(.+)%'=>function($e){return '<meta name="twitter:description" content="'.$e[1].'"/>';},
    '%META_TWITTER_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta name="twitter:image" content="'.$e[1].'"/>';},
    '%META_OG_TITLE=(.+)%'=>function($e){return '<meta property="og:title" content="'.$e[1].'"/>';},
    '%META_OG_DESCRIPTION=(.+)%'=>function($e){return '<meta property="og:description" content="'.$e[1].'"/>';},
    '%META_OG_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta property="og:image" content="'.$e[1].'"/>';},
    '%META_OG_URL=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta property="og:url" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_NAME=(.+)%'=>function($e){return '<meta itemprop="name" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_DESCRIPTION=(.+)%'=>function($e){return '<meta itemprop="description" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta itemprop="image" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_URL=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta itemprop="url" content="'.$e[1].'"/>';},
    '%META=(.+)%'=>function($e){return '<meta '.$e[1].'/>';}
)) : '');
(!defined('NW_DICTIONARY_HOOKS') ? define('NW_DICTIONARY_HOOKS',array(
    '%CONFIG=(.*?)%'=>function($e){
        $footerjs='';
        foreach((new Plugins())->list() as $plugin){
            $footerjs.=(new Plugins($plugin))->init()->setPlacement('config')->setArgs(...explode(';',$e[1]))->exec();
        }
        return $footerjs;
    },
    '%HEAD%'=>function(){
        $heading='';
        foreach((new Plugins())->list() as $plugin){
            $heading.=(new Plugins($plugin))->init()->setPlacement('head')->setArgs()->exec();
        }
        return $heading;
    },
    '%BEFORELOAD%'=>function(){
        $bLoad='';
        foreach((new Plugins())->list() as $plugin){
            $bLoad.=(new Plugins($plugin))->init()->setPlacement('beforeLoad')->setArgs()->exec();
        }
        return $bLoad;
    },
    '%AFTERLOAD%'=>function(){
        $aLoad='';
        foreach((new Plugins())->list() as $plugin){
            $aLoad.=(new Plugins($plugin))->init()->setPlacement('afterLoad')->setArgs()->exec();
        }
        return $aLoad;
    },
    '%FOOTER%'=>function(){
        $footer='';
        foreach((new Plugins())->list() as $plugin){
            $footer.=(new Plugins($plugin))->init()->setPlacement('footer')->setArgs()->exec();
        }
        return $footer;
    },
    '%FOOTERJS%'=>function(){
        $footerjs='';
        foreach((new Plugins())->list() as $plugin){
            $footerjs.=(new Plugins($plugin))->init()->setPlacement('footerjs')->setArgs()->exec();
        }
        return $footerjs;
    }
)) : '');
(!defined('NW_DICTIONARY_LANG') ? define('NW_DICTIONARY_LANG', array(
    '%LANG=(.+?)%'=>function($e){$s = explode(',',$e[1]); return (new Lang())->get($s[0],$s[1]);},
    '%PATH=(.+?)%'=>function($e){return (new Web(constant($e[1])))->toAccessable();}
)) : '');
(!defined('NW_DICTIONARY_PAGES') ? define('NW_DICTIONARY_PAGES', array(
    '%LISTPAGES%'=>function(){
        $noShow = array('install.php','.htaccess','config.php','init.php');
        $out='';
        foreach(array_diff(scandir(NW_ROOT),['.','..']) as $page){
            if(is_file(NW_ROOT.NW_DS.$page)&&!in_array($page,$noShow)){
                $out.='<li class="nav-item">
                    <a class="nav-link '.(isset((new Web())->getPath()[1]) ? (strtolower($page)===strtolower((new Web())->getPath()[count((new Web())->getPath()) - 1]) ? 'active' : '') : (strtolower($page)==='home.php' ? 'active' : '')).'" aria-current="page" href="'.($page==='home.php' ? './' : preg_replace('/\..*?$/','',$page)).'">'.ucfirst(preg_replace('/\..*?$/','',$page)).'</a>
                </li>';
            }
        }
        return $out;
    },
    '%DOCERROR%'=>function(){
        return http_response_code();
    },
    '%PAGELANG%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['lang'])[0]['lang'];
            $sql->close();
            return explode('-',$data)[0];
        }
    }
)) : '');
(!defined('NW_DICTIONARY_CONFIG') ? define('NW_DICTIONARY_CONFIG',array(
    '%WEBTITLE%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['title'])[0]['title'];
            $sql->close();
            return $data;
        }
    }
)) : '');
/**
 * A variable dictionary for templates
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Dictionary{
    protected $ui = array();
    /**
     * Creates a variable dictionary
     */
    public function __construct() {
        # Nothing
    }
    /**
     * Adds an item to Dictionary
     *
     * @param String $search Search for a target query. Ex: **%USERNAME%**
     * @param Callable $replace [Optional] - Replace the search with a value. Ex: **JohnDoe**
     * @return this
     */
    public function addItem(String $search, callable $replace) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        if(!in_array($search,$this->ui))
            array_push($this->ui,['/'.$search.'/'=>$replace]);
        return $this;
    }
    /**
     * Drop and item from the dictionary
     *
     * @param String $search Search query to drop. Ex: **%USERNAME%**
     * @return $this
     */
    public function dropItem(String $search) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        if(in_array($search,$this->ui)) unset($this->ui['/'.$search.'/']);
        return $this;
    }
    /**
     * Sanitizes the array
     *
     * @return array
     */
    private function sanitize():array{
        foreach ($this->ui as $key => $value) {
            // Check if the key matches the format %KEY%
            if (preg_match('/^%(.+)%$/', $key, $matches)) {
                // Extract the inner key
                $newKey = $matches[1]; // This is 'KEY' from %KEY%
                
                // Replace with the original key and keep the value
                // You can modify this part to decide what you want to replace the keys with
                $this->ui[$newKey] = $value;
                unset($this->ui[$key]); // Remove the old key
            }
        }
        return array_merge(...$this->ui);
    }
    /**
     * List everything in the dictionary
     *
     * @return Array{Search: String}
     */
    public function listItem() : array{
        return $this->sanitize();
    }
    /**
     * Replace an item in the array
     *
     * @param String $search Search to look for. Ex: **%USERNAME%**
     * @param String $newSearch Search to replace with. Ex: **%NAME%**
     * @param Callable|null $replace [Optional] - Replace the value, leave _null_ to set as default replace
     * @return void
     */
    public function replaceItem(String $search, String $newSearch, callable|null $replace=null) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        $newSearch = preg_replace('/^\/|\/$/','',$newSearch);
        if(in_array($search,$this->ui)){
            $this->ui['/'.$newSearch.'/'] = ($replace ?  $replace : $this->ui['/'.$search.'/']);
            unset($this->ui['/'.$search.'/']);
        }
        return $this;
    }
    /**
     * Merages multiple dictionaries
     *
     * @param Array<String> ...$dict
     * @return array Merged dictionaries
     */
    public function merge(...$dict) : array {
        return array_merge(...$dict);
    }
}
?>
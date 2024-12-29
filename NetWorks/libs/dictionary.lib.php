<?php
namespace networks\libs;
use networks\libs\Users;
use networks\libs\Lang;
use networks\libs\Plugins;
use networks\libs\Web;
use networks\libs\HTMLForm;
use SSQL;

require_once(dirname(__DIR__).'/init.php');
(!defined('NW_DICTIONARY_USER') ? define('NW_DICTIONARY_DEFAULT',array(
    '%USER_LANGUAGE%'=>function(){return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);},
    '%USER_DATETIME%'=>function(){return date('Y-m-d H:i:s');},
    '%USER_DATE%'=>function(){return date('Y-m-d');},
    '%USER_TIME%'=>function(){return date('H:i:s');},
    '%USER_IP%'=>function(){return (new Users())->IP()['ip'];},
    '%USER_IP_VISIBILITY%'=>function(){return (new Users())->IP()['visibility'];},
    '%USER_IS_ONLINE%((.|\n)*?)%END%'=>function($e){if(isset($_COOKIE['user'])) return $e[1];},
    '%USER_IS_OFFLINE%((.|\n)*?)%END%'=>function($e){if(!isset($_COOKIE['user'])) return $e[1];}
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
        $out='';
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            foreach($db->selectData('pages',['*']) as $page){
                if(is_file(NW_ROOT.NW_DS.$page['pageName'].'.php')){
                    $out.='<li class="nav-item d-flex align-items-center px-2">
                        <i class="'.$page['pageIcon'].'"></i>
                        <a class="nav-link '.(isset((new Web())->getPath()[1]) ? (strtolower($page['pageName'])===strtolower((new Web())->getPath()[count((new Web())->getPath()) - 1]) ? 'active' : '') : (strtolower($page['pageName'])==='home.php' ? 'active' : '')).'" aria-current="page" href="'.($page['pageName']==='home' ? './' : $page['pageName']).'">'.ucfirst($page['pageName']).'</a>
                    </li>';
                }
            }
        }
        return $out;
    },
    '%DOCERROR%'=>function(){
        return http_response_code();
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
    },
    '%CONFIGLANG%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['lang'])[0]['lang'];
            $sql->close();
            return explode('-',$data)[0];
        }
    },
    '%DEBUG%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['debug'])[0]['debug'];
            $sql->close();
            return $data;
        }
    },
    '%DATEFORMAT%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['dFormat'])[0]['dFormat'];
            $sql->close();
            return $data;
        }
    },
    '%DATEFORMAT%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['dFormat'])[0]['dFormat'];
            $sql->close();
            return $data;
        }
    },
    '%THEME%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['theme'])[0]['theme'];
            $sql->close();
            return $data;
        }
    },
    '%EDITOR%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['editor'])[0]['editor'];
            $sql->close();
            return $data;
        }
    },
)) : '');
(!defined('NW_DICTIONARY_CONDITIONS') ? define('NW_DICTIONARY_CONDITIONS', array(
    '%URL_PATH=(.+?)%((.|\n)*?)%END%'=>function($e){
        $url = (new Web())->getPath();
        if($url[0])
            unset($url[0]);
        if(strcmp(strtolower($e[1]),strtolower(implode('/',array_values($url))))==0){
            return $e[2];
        }
    }
)) : '');
(!defined('NW_DICTIONARY_FORMS') ? define('NW_DICTIONARY_FORMS', array(
    '%FORM(=(.+?))?%((.|\n)*?)%ENDFORM%'=>function($e){
        $e = array_values(array_filter($e,function($e){return trim($e)!=='';}));
        $form = (new HTMLForm());
        foreach(preg_split('/\r\n|\n/',trim(preg_replace('/\t/','',(isset($e[3]) ? $e[3] : $e[1])))) as $elem){
            foreach(NW_DICTIONARY_FORMS_ELEMENTS as $patt=>$call){
                if(preg_match('/'.$patt.'/',$elem)){
                    $elem = trim(preg_replace_callback('/'.$patt.'/',$call,$elem));
                    $args = explode(';',$elem);
                    if(count($args)>1){
                        $setMethod = $args[0];
                        unset($args[0]);
                        $args = (new Utils())->extractParam($args);
                        $form->{$setMethod}(...$args);
                    }else
                        $form->{$args[0]}();
                    break;
                }
            }
        }
        if(isset($e[3])) $formArgs = explode(';',$e[2]);
        if(isset($formArgs)){
            $method='post';
            $action='';
            $enctype='';
            $class='';
            foreach($formArgs as $fa){
                $fa = trim(preg_replace('/[\r\n]+/','',$fa));
                preg_match('/action:(.+);?/',$fa,$matches);
                if(preg_match('/method:(.+);?/',$fa,$matches)) $method = $matches[1];
                if(preg_match('/action:(.+);?/',$fa,$matches)) $action = $matches[1];
                if(preg_match('/enctype:(.+);?/',$fa,$matches)) $enctype = $matches[1];
                if(preg_match('/class:(.+);?/',$fa,$matches)) $class = $matches[1];
            }
            return $form->finalize($method,$action,$enctype,$class);
        }else
            return $form->finalize();
    }
)) : '');
(!defined('NW_DICTIONARY_FORMS_ELEMENTS') ? define('NW_DICTIONARY_FORMS_ELEMENTS', array(
    '%ROW(=class:(.+?))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/^=/','',$e);
        },$e);
        $out='row;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%COL(=class:(.+?))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/^=/','',$e);
        },$e);
        $out='col;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%TITLE=(value:(.+?))%'=>function($e){
        $out = 'title;';
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        
        foreach($e as $txt){
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%text=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='text;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%password=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='password;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%button=(name:(.+?));?(type:(.*?));?(class:(.*?));?(link:(.*?));?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $out='button;';
        foreach($e as $txt){
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%color=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='color;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    }
    ,
    '%recaptcha=(value:(.*?))%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $out='reCAPTCHA;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
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
     * @return Dictionary
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
     * @return Dictionary
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
     * @return Dictionary
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
     * @param Array<String> ...$dict Dictionary to convert into constants
     * @return array Merged dictionaries
     */
    public function merge(...$dict) : array {
        return array_merge(...$dict);
    }
    /**
     * Undocumented function
     *
     * @param Array<String> ...$dict Dictionary to convert into constants
     * @return void
     */
    public function toConst(...$dict):void{
        $dict = array_merge(...$dict);
        foreach($dict as $d=>$f){
            if(!defined($d)) define($d,$f);
        }
    }
}
?>
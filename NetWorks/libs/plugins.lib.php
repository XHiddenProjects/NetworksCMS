<?php
namespace networks\libs;

use networks\Exception\PluginHandlingException;
require_once(dirname(__DIR__).'/init.php');
use SSQL;
/**
 * Plugin compiler
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Plugins{
    protected String|null $plugin;
    protected String $placement;
    protected array $args;
    protected bool $isInit=false, $active=false, $disable=false;
    protected bool $isSupported = false;
    /**
     * Create a plugin class
     *
     * @param string $pname Plugins name
     * @param bool|null $status [Optional] - Plugins status
     * @param bool|null $disable [Optional] - Disables the active status
     */
    public function __construct(string|null $pname=null, bool|null $status=false, bool|null $disable=false) {
        $this->plugin = $pname??'';
        $this->active = $status??false;
        $this->disable = $disable??false;
        $currentVersion = file_get_contents('https://raw.githubusercontent.com/XHiddenProjects/NetWorks/refs/heads/master/VERSION');
        $isEnded=false;
        $index=0;
        if($this->plugin){
            $code = json_decode(file_get_contents(NW_PLUGINS.NW_DS.$this->plugin.NW_DS.'lang'.NW_DS.(new Lang())->current().'.json'),true);
            if(isset($code['support'])){
                while(!$this->isSupported&&!$isEnded){
                    if(version_compare(trim($code['support'][$index]), trim($currentVersion),'==')) $this->isSupported = true;
                    else $index++;
                    if(count($code['support'])==$index) $isEnded = true;
                }
                if(!$this->isSupported) $this->update(false);
            }else die($this->plugin.(new Lang())->get('Errors','noPluginSupportObj'));
        }
    }
    /**
     * Initiate the plugin
     *
     * @return Plugins
     */
    public function init():Plugins{
        if(file_exists(NW_SQL_CREDENTIALS)&&$this->plugin){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if(!$db->selectData('plugins',['*'],"WHERE pluginName=\"{$this->plugin}\""))
                    $db->addData('plugins',['pluginName','pluginStatus','pluginDisabled'], [$this->plugin,(int)$this->active,(int)$this->disable]);
                $this->isInit = true;
            }
        }else $this->isInit = true;
        return $this;
    }
    /**
     * Set a target to trigger the function
     *
     * @param string $target Result placement
     * @return Plugins|false
     */
    public function setPlacement(String $target):Plugins|false{
        if($this->isInit){
            $this->placement = $target;
            return $this;
        }else return false;
    }
    /**
     * Give arguments to your method
     *
     * @param mixed ...$args Arguments
     * @return Plugins|false
     */
    public function setArgs(...$args):Plugins|false{
        if($this->isInit&&$this->placement){
            $this->args = $args;
            return $this;
        }else return false;
    }
    /**
     * Checks if method exists
     *
     * @param object|string $class Plugins to look for
     * @param string $method PLugins method
     * @return boolean TRUE if exists, otherwise FALSE
     */
    public function isExists(object|string $class, string $method):bool{
        if(method_exists($class,$method)) return true;
        return false;
    }
    /**
     * Checks if the plugin is active
     *
     * @return bool
     */
    public function isActive():bool{
        $active = false;
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $active = $db->selectData('plugins',['*'],"WHERE pluginName=\"{$this->plugin}\"");
            }
        }
        if(isset($active[0])){
            if($active[0]['pluginStatus']) return true;
            else return false;
        }else return $this->active;
    }
    public function isDisabled():bool{
        $disabled = false;
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $disabled = $db->selectData('plugins',['*'],"WHERE pluginName=\"{$this->plugin}\"");
            }
        }
        if(isset($disabled[0])){
            if($disabled[0]['pluginDisabled']) return true;
            else return false;
        }else return $this->disable;
    }
    /**
     * Updates the active status
     * @param bool|null $forceStatus forces the status
     * @return bool TRUE if success updated, else FALSE
     */
    public function update(bool|null $forceStatus=null, bool $forceDisable=false): bool{
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if($forceStatus==null){
                    $a = $db->selectData('plugins',['*'],"WHERE pluginName=\"{$this->plugin}\"");
                    if($db->updateData('plugins',"pluginStatus=".($a[0]['pluginStatus'] ? 0 : 1).', pluginDisabled='.($a[0]['pluginDisabled'] ? 0 : 1), "pluginName=\"{$this->plugin}\""))
                        return true;
                }else{
                    if($db->updateData('plugins',"pluginStatus=".($forceStatus ? 1 : 0).', pluginDisabled='.($forceDisable ? 1 : 0), "pluginName=\"{$this->plugin}\""))
                        return true;
                }
            }
        }
        return false;
    }
    /**
     * Initializes the plugin
     * @param bool $forceStatus Plugin activation status
     * @param bool $forceDisable Plugin disable status
     * @return bool Confirms if it successfully started
     */
    public function start(bool $forceStatus, bool $forceDisable): bool{
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                if($db->selectData('plugins',['*'],"WHERE pluginName=\"{$this->plugin}\" AND pluginInit=0")){
                    if($db->updateData('plugins',"pluginStatus=".((int)$forceStatus).', pluginDisabled='.((int)$forceDisable).', pluginInit=1', "pluginName=\"{$this->plugin}\""))
                        return true;
                }
            }
        }
        return false;
    }
    /**
     * List all the plugins in the _plugins_ folder
     *
     * @return array
     */
    public function list():array{
        return array_values(array_diff(scandir(NW_PLUGINS),['.','..']));
    }
    /**
     * Executes the plugin
     *
     * @return mixed returns plugin results, otherwise it's NULL
     */
    public function exec():mixed{
        try{
            if($this->isInit&&$this->plugin&&$this->placement){
                if($this->isExists($this->plugin,$this->placement))
                    return call_user_func(array(new $this->plugin(),$this->placement),$this->args);
                else return null;
            }else
                throw new PluginHandlingException('has not been initiated correctly.',$this->plugin);
        }catch(PluginHandlingException $e){
            echo '<b>NetWorks Plugin_Handler:</b> '.$e->getPluginStats().' '.$e->getMessage().' on line '.$e->getLine();
            return null;
        }
    }

    public function createConfig(string $table, array $items, array $types, array $values, array $options): void{
        $sql = new SSQL();
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $db->makeTable($table,$items,$types,$values,$options);
            }
            $sql->close();
        }
    }

}
?>
<?php
namespace networks\libs;

use networks\Exception\PluginHandlingException;
require_once(dirname(__DIR__).'/init.php');
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
    protected bool $isInit=false;
    protected bool $active=false;
    /**
     * Create a plugin class
     *
     * @param string|null $pname Plugins name
     */
    public function __construct(String|null $pname=null) {
        $this->plugin = $pname;
    }
    /**
     * Initiate the plugin
     *
     * @return Plugins
     */
    public function init():Plugins{
        $this->isInit = true;
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
        if($this->active) return true;
        else return false;
    }
    /**
     * List all the plugins in the _plugins_ folder
     *
     * @return array
     */
    public function list():array{
        return array_diff(scandir(NW_PLUGINS),['.','..']);
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
}
?>
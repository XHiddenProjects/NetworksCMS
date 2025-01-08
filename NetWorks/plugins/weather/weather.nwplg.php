<?php
use networks\libs\Plugins;
use networks\libs\Web;
use networks\libs\Lang;
require_once dirname(__DIR__,2).'/init.php';
class Weather extends Plugins{
    public function __construct(){
        $this->plugin = 'weather';
        $this->active = false;
        $this->disable = false;
    }
    public function head(){
        if($this->isActive()){
            return '<link rel="stylesheet" href="'.(new Web(NW_PLUGINS.NW_DS.$this->plugin.NW_DS.'css'.NW_DS.$this->plugin.'api.css'))->toAccessible().'"/>';
        }return '';
    }
    public function afterMain(): string{
        if($this->isActive()){
            $paths = (new Web())->getPath();
            $out = '';
            if(count($paths)<=2&&$paths[count($paths)-1]==='dashboard'){
                $out.='<div class="weather_api" weather-pos="bottom left">
                            <div class="weather_location"></div>
                            <div class="wrapper">
                                <img class="weather_api_img"/>
                                <div class="weather_api_desc"></div>
                            </div>
                            <a class="weather_api_powerBy" href="https://www.weather.gov/documentation/services-web-api" target="_blank">'.(new Lang())->get('poweredByGov').'</a>
                        </div>';
            }
            return $out;
        }else return '';
    }
    public function footerJS():string {
        if($this->isActive()){
            return '<script src="'.(new Web(NW_PLUGINS.NW_DS.$this->plugin.NW_DS.'js'.NW_DS.$this->plugin.'api.js'))->toAccessible().'"></script>';
        }return '';
    }
}
?>
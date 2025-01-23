<?php
use networks\libs\Plugins;
use networks\libs\Web;
use Detection\Exception\MobileDetectException;
use Detection\MobileDetectStandalone;

require_once dirname(__DIR__,2).'/init.php';
require_once dirname(__DIR__,2).'/libs/Mobile-Detect/standalone/autoloader.php';
require_once dirname(__DIR__,2).'/libs/Mobile-Detect/src/MobileDetectStandalone.php';

class Core extends Plugins{
    protected string $theme;
    public function __construct() {
        $this->plugin = 'core';
        $this->active = true;
        $this->disable = true;
        # Configurations here
        $this->theme = 'default';
        $this->start($this->active,$this->disable);
    }
    public function head(): string{
        if($this->isActive()){
            $detection = new MobileDetectStandalone();
            $out = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined|Material+Symbols+Rounded|Material+Symbols+Sharp" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/XHiddenProjects/WebAnimate@0.0.6/assets/webanimate.min.css"/>
            <link rel="stylesheet" href="https://unpkg.com/alwan/dist/css/alwan.min.css">';
            foreach(array_diff(scandir(NW_THEMES.NW_DS.$this->theme.NW_DS.'css'),['.','..']) as $file){
                $sfile = preg_replace('/\.(.*?)$/', '',$file);
                $p = (new Web())->getPath()[1] ?? (new Web())->getPath()[0];
                if($p==='NetWorks') $p = 'home';
                if((($sfile===$p)||$sfile==='mobile')&&$sfile!=='reset'){
                    if(!preg_match('/-ignore/',$file))
                        $out.='<link rel="stylesheet" href="'.(new Web(NW_THEMES.NW_DS.$this->theme.NW_DS.'css'.NW_DS.$file))->toAccessible().'"/>';
                }
            }
            if(file_exists(NW_SQL_CREDENTIALS)){
                $ssql = new SSQL();
                $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
                if($ssql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                    $db = $ssql->selectDB($cred['db']);
                    $selectIcons = $db->selectData('config',['ico16','ico24','ico32','ico48','ico64','ico96','ico256','ico512']);
                    if($selectIcons){
                        foreach($selectIcons[0] as $size=>$path){
                            if($detection->isMobile()){
                                if((int)str_replace('ico','',$size)==64)
                                $out.='<link rel="icon" type="image/x-icon" href="'.$path.'" sizes="'.str_replace('ico','',$size).'x'.str_replace('ico','',$size).'"/>';
                            }elseif($detection->isTablet()){
                                if((int)str_replace('ico','',$size)==128)
                                    $out.='<link rel="icon" type="image/x-icon" href="'.$path.'" sizes="'.str_replace('ico','',$size).'x'.str_replace('ico','',$size).'"/>';
                            }else{
                                if((int)str_replace('ico','',$size)==16)
                                    $out.='<link rel="icon" type="image/x-icon" href="'.$path.'" sizes="'.str_replace('ico','',$size).'x'.str_replace('ico','',$size).'"/>';
                            }
                            
                        }
                    }
                }
            }
            $out.='<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>';
            return $out;
        }else return '';
    }
    public function footerJS(): string{
        if($this->isActive()){
            $out = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/alwan/dist/js/alwan.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

            if(file_exists(NW_SQL_CREDENTIALS)){
                $sql = new SSQL();
                $c = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
                if($sql->setCredential($c['server'],$c['user'],$c['psw'])){
                    $db = $sql->selectDB($c['db']);
                    $captcha = $db->selectData('recaptcha',['*']);
                    if($captcha[0]['reCAPTCHA_active']){
                        if(strtolower($captcha[0]['reCAPTCHA_version'])==='v3')
                            $out.='<script src="https://www.google.com/recaptcha/api.js"></script>';
                        else
                            $out.='<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                    }
                }
            }

            foreach(array_diff(scandir(NW_ASSETS.NW_DS.'js'),['.','..']) as $file){
                if(!preg_match('/-ignore/',$file))
                    $out.='<script src="'.((new Web(NW_ASSETS.NW_DS.'js'.NW_DS.$file))->toAccessible()).'"></script>';
            }
            foreach(array_diff(scandir(NW_THEMES.NW_DS.$this->theme.NW_DS.'js'),['.','..']) as $file){
                if(!preg_match('/-ignore/',$file))
                    $out.='<script src="'.((new Web(NW_THEMES.NW_DS.$this->theme.NW_DS.'js'.NW_DS.$file))->toAccessible()).'"></script>';
            }
            return $out;
        }else return '';
    }
}
?>
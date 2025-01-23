<?php
use networks\libs\Plugins;
use networks\libs\Web;
use networks\libs\Lang;
require_once dirname(__DIR__,2).'/init.php';

class Redirect extends Plugins{
    public function __construct() {
        $this->plugin = 'redirect';
        $this->active = false;
        $this->disable = false;
        $this->start($this->active,$this->disable);
    }
    public function afterMain(): string{
        $out = '';
        if($this->isActive()){
            $out .= '<div class="modal fade" id="redirectWarn" tabindex="-1" aria-labelledby="redirectWarn" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="redirectWarn">'.(new Lang())->get('redirectWarnTitle').'</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    '.((new Lang())->get('redirectWarnMsg')).' <span class="url-location text-secondary fst-italic"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" aria-label="close" class="btn btn-secondary" data-bs-dismiss="modal">'.((new Lang())->get('Buttons','close')).'</button>
                    <button type="button" aria-label="continue" class="btn btn-warning redirectContinue">'.((new Lang())->get('Buttons','continue')).'</button>
                </div>
                </div>
            </div>
            </div>';
        }
        return $out;
    }
    public function footerJS(): string{
        $out = '';
        if($this->isActive()){
            $out.= '<script src="'.(new Web(NW_PLUGINS.NW_DS.$this->plugin.NW_DS.'js'.NW_DS.$this->plugin.'.min.js'))->toAccessible().'"></script>';
        }
        return $out;
    }
}
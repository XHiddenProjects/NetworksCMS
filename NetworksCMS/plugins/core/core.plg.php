<?php
namespace NetWorks\Plugins;
use NetWorks\libs\Files;
include_once dirname(path: __DIR__,levels: 2).'/init.php';
use NetWorks\libs\Plugins;
use NetWorks\libs\Database;

class core extends Plugins{
    protected Database $db;
    protected Files $f;
    protected string $name = 'core';
    protected Database $table;
    protected array $selection=[];
    public function __construct() {
        $this->f = new Files();
        if($this->dbExits()){
            $this->db = new Database(file: 'NetworksCMS',flags: Database::OPEN_READWRITE);
            $this->table = $this->db->selectTable(name: 'plugins');
            $this->selection = $this->selFormat(selection: $this->table->select(conditions: "plugin_name=\"{$this->name}\"",mode: Database::ASSOC));
        }
    }
    public function install(): void{
        if(empty($this->selection)){
            $this->table->insert(name: null,data: ['plugin_name'=>'core','plugin_status'=>1,'plugin_disabled'=>1]);
        }
    }
    public function css(): string{
        if(empty($this->selection)||$this->selection['plugin_status']==0) return '';
        else{
            $out='';
            $out.="<link rel=\"stylesheet\" href=\"".NW_PLUGIN."/$this->name/css/$this->name.min.css\">";
            return $out;
        }
    }
    public function afterLoad(): string{
        global $lang;
        if(empty($this->selection)||$this->selection['plugin_status']==0) return '';
        else return '<div class="cookieNotice">
            <div>
                <i class="material-symbols-rounded">cookie</i> '.$lang['cookieNotice'].'
            </div>
            <div>
                <button class="btn btn-light" id="cookieAccept">'.$lang['accept'].'</button>
            </div>
        </div>';
    }
    public function footerJS(): string{
            $files = new Files();
            $out='';
            if(isset($this->db))
                $theme = $this->db->selectTable(name: 'settings')->select(selector: 'theme',mode: Database::ASSOC);
            foreach($files->scan(dir: NW_THEMES_DIR."/".($theme['theme']??'default')."/js") as $themes)
                $out.="<script".(stripos(haystack: $themes,needle: 'module') ? ' type="module"' : '')." src=\"".NW_THEMES."/".($theme['theme']??'default')."/js/$themes\"></script>";
            $out.="<script src=\"".NW_PLUGIN."/$this->name/js/$this->name.min.js\"></script>";
            return $out;
    }
}
?>
<?php
namespace NetWorks\libs;
include_once dirname(path: __DIR__).'/init.php';
use NetWorks\libs\Plugins;
use NetWorks\libs\Database;
use NetWorks\libs\HTMLForm;
use NetWorks\libs\Users;
use NetWorks\libs\Utils;
class Templates{
    protected Plugins $plugins;
    protected Database $db;
    protected Users $users;
    protected Utils $utils;
    protected array $keywords = [];
    public function __construct() {
            $this->plugins = new Plugins();
            $this->users = new Users();
            $this->utils = new Utils();
            if(file_exists(filename: NW_DATABASE.NW_DS.'NetworksCMS.db'))
                $this->db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
            $this->keywords = [
                '/<hook>(.+?)(;.+?)?<\/hook>/i'=>function($e): string{
                    if(isset($e[1])){
                        if(isset($e[2])) {
                            $e[2] = preg_replace(pattern: '/^;/',replacement: '',subject: trim(string: $e[2]));
                            return $this->plugins->hook(hookName: $e[1],args: explode(separator: ',',string: $e[2]));
                        }else{
                            $e[1] = preg_replace(pattern: '/;$/',replacement: '',subject: trim(string: $e[1]));
                            return $this->plugins->hook(hookName: $e[1]);
                        }
                    }else return '';
                },
                '/<lang>(.*?)<\/lang>/i'=>function($e): string{
                    global $lang;
                    if(isset($e[1])){
                        $langs = explode(separator: '|',string: $e[1]);
                        if(file_exists(filename: NW_DATABASE.NW_DS.'NetworksCMS.db'))
                            $results = $this->db->selectTable(name: 'settings')->select();
                            foreach($langs as $l){
                            if(isset($lang[$l])||isset($results[$l])){
                                return $results[$l]??$lang[$l];
                            }
                        }
                        return '';
                    }else return '';
                },
                '/<page href="(.*?)" icon="(.*?)">(.*?)<\/page>/i'=>function($e): string{
                    global $lang;
                    if(isset($e[1])){
                        return "<li class=\"nav-list\">
                            <a class=\"nav-link\" href=\"$e[1]\"><i class=\"material-symbols-outlined\">$e[2]</i> ".$lang[$e[3]]."</a>
                        </li>";
                    }else return '';
                },
                '/<copyrightDate>([\d]{4})<\/copyrightDate>/i'=>function($e): string{
                    return "&copy; $e[1]" . ((int)date(format: 'Y')>$e[1] ? ' - '.date(format: 'Y') : '');
                },
                '/<nwform(.*?)?>((.|\n)*?)<\/nwform>/i'=>function($e): string{
                    $e = array_filter(array: $e,callback: function($e): bool{return !empty(trim(string: $e));});
                    $form = new HTMLForm();
                    preg_match_all(pattern: '/ (.*?)=\"(.*?)\"/',subject: $e[1],matches: $attrs);
                    unset($attrs[0]);
                    $attrs = array_combine(keys: $attrs[1],values: $attrs[2]);
                    $attrs['controls'] = $this->build(controls: $e[2]);
                    return call_user_func_array(callback: [$form,'form'],args: $attrs);
                },
                # Constants
                '/{{ROOT}}/'=>function(): string{
                    return NW_DOMAIN;
                },
                # Conditions
                '/<isLoggedIn>((.|\n)*?)<\/isLoggedIn>/i'=>function($e):string{
                    if((new Users())->get()) return $e[1];
                    else return '';
                },
                '/<isLoggedOut>((.|\n)*?)<\/isLoggedOut>/i'=>function($e):string{
                    if((new Users())->get()) return '';
                    else return $e[1];
                },
                '/<isURLQuery target=\"(.*?)\">((.|\n)*?)<\/isURLQuery>/i'=>function($e):string{
                    if(isset($_GET[$e[1]])) return $e[2];
                    else return '';
                },
                '/<isAdmin>((.|\n)*?)<\/isAdmin>/'=>function($e): string{
                    if($this->users->isAdmin()) return $e[1];
                    else return '';
                },
                '/<isMod>((.|\n)*?)<\/isMod>/'=>function($e): string{
                    if($this->users->isMod()) return $e[1];
                    else return '';
                },
                '/<isMember>((.|\n)*?)<\/isMember>/'=>function($e): string{
                    if($this->users->isMember()) return $e[1];
                    else return '';
                },
                '/<isPath path="(.*?)">((.|\n)*?)<\/isPath>/'=>function($e): string{
                    $e[1] = $this->utils->sanitizeSlashes(str: $e[1]);
                    if(preg_match(pattern: "/$e[1]$/",subject: implode(separator: '/',array: NW_PATH_ARRAY))) return $e[2];
                    else return '';
                }
            ];
    }
    /**
     * Builds the form
     * @param string $controls
     * @return string Form controls build
     */
    protected function build(string $controls): string{
        $form = new HTMLForm();
        $attr='';
        preg_match_all(pattern: '/<(.+?)( .*?)?>(.*?)<\/(.+?)>/', subject: $controls, matches: $controllers);
        foreach ($controllers[1] as $index => $tag) {
            $attributes = [];
            if (!empty($controllers[2][$index])) {
                preg_match_all(pattern: '/(\w+)="(.*?)"/', subject: $controllers[2][$index], matches: $attrMatches);
                foreach ($attrMatches[1] as $attrIndex => $attrName) {
                    $attributes[$attrName] = $attrMatches[2][$attrIndex];
                }
            }
            global $attr;
            $attr = '';
            array_walk(array: $attributes,callback: function(&$v, $k): void{
                global $attr;
                $attr .= " {$k}=\"{$v}\"";
            });

            $escapedAttr = preg_quote(str: trim(string: $attr), delimiter: '/');

            if (method_exists(object_or_class: $form, method: $tag)) 
                $controls = preg_replace(pattern: "/<{$tag} {$escapedAttr}>(.*?)<\/{$tag}>/s", replacement: call_user_func_array(callback: [$form, $tag], args: $attributes), subject: $controls);
        }
        return $controls;
    }
    /**
     * Loads the template
     * @param string $name
     * @return array|bool|string Template
     */
    public function load(string $name):array|bool|string{
        $file = file_get_contents(filename: NW_TEMPLATES.NW_DS.preg_replace(pattern: '/\?.*$/',replacement: '',subject: $name).'.html');
        foreach($this->keywords as $pattern=>$callback)
                $file = preg_replace_callback(pattern: $pattern,callback: $callback,subject: $file);
        if(isset($this->db)){
            $this->db->close();
        }
        return $file??'';
    }
}
?>
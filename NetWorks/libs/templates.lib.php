<?php
namespace networks\libs;
use networks\libs\Dictionary;

use networks\Exception\FileHandlingException;
use networks\libs\Lang;

require_once dirname(__DIR__).'/init.php';


/**
 * Networks Template compiler
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Templates{
    protected String $name, $path=NW_TEMPLATES;
    protected Lang $lang;
    /**
     * Generates HTML template from template/...
     *
     * @param string $tname
     */
    public function __construct(string $tname) {
        $this->name = $tname;
        $this->lang = new Lang();
    }
    /**
     * Changes the dictionary for the themes.
     *
     * @param string $path Folder path the change to.
     * @return void
     */
    public function chDir(string $path){
        try{
            if(file_exists($path)&&is_dir($path))
                $this->path = $path;
            else throw new FileHandlingException($this->lang->get('Errors','noDir'),$path);
        }catch(FileHandlingException $e){
            echo '<b>NetWorks File_Handling:</b> '.$e->getMessage().' <em>'.$e->getPath().'</em> on line '.$e->getLine();
        }
    }
    /**
     * Undocumented function
     *
     * @param string $compile HTML to compile
     * @param array $setDict Dictionary replacement
     * @return string Compiled HTML format
     */
    private function compile(string $compile, array $setDict): mixed{
        $dict = new Dictionary();
        foreach($setDict as $key=>$val) $dict->addItem($key,$val);
        foreach($dict->listItem() as $key=>$value){
            $compile = preg_replace_callback($key,$value,$compile);
        }
        return $compile;
    }
    /**
     * minifies the HTML string
     * @param string $html HTML to minify
     * @return string Minified HTML
     */
    private function minify(string $html): string{
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/[\n\t\v]+/', ' ', $html);
        $html = trim($html);
        return $html;
    }


    /**
     * Loads the HTML template
     * @param array<string> $dict Dictionary to use as a lookup
     * 
     * @return string|bool Returns the HTML string, False if the file doesn't exist
     */
    public function load(array $dict): string|bool{
        if(file_exists($this->path.NW_DS.$this->name.'.html')) 
            return $this->minify($this->compile(file_get_contents($this->path.NW_DS.$this->name.'.html'), $dict));
        else
            return false;
    }
}
?>
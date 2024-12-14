<?php
namespace networks\libs;

use networks\Exception\NumberHandlingException;
use networks\libs\Lang;
use SSQL;
require_once(dirname(__DIR__).'/init.php');
/**
 * HTML Form
 */
class HTMLForm{
    protected string $charset = NW_CHARSET;
    protected array $lang;
    protected array $elements=[];
    protected int $rowIndex, $colsIndex;
    public function __construct() {
        # nothing
        if(file_exists(NW_SQL_CREDENTIALS)){
            $sql = new SSQL();
            $f = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            if($sql->setCredential($f['server'],$f['user'],$f['psw'])){
                $db = $sql->selectDB($f['db']);
                $this->charset = $db->selectData('config',['charset'])[0]['charset'];
                $this->lang = explode('-',$db->selectData('config',['lang'])[0]['lang']);
            }
            $sql->close();
        }
        return $this;
    }
    /**
     * Cleans the users input
     *
     * @param string $txt Input
     * @return string sanitized string
     */
    public function clean(string $txt):string{
        return htmlspecialchars(trim($txt ?? ''), ENT_QUOTES, $this->charset);
    }
    /**
     * Fixes Newlines
     *
     * @param string $txt Users input
     * @return string fixed newlines
     */
    public function transNL(string $txt):string{
        return preg_replace('/\n{3,}/', "\n\n", str_replace(array("\r\n", "\r"), "\n", $txt));
    }
    /**
     * Adds a row
     *
     * @return HTMLForm
     */
    public function row():HTMLForm{
        if(!isset($this->rowIndex)) $this->rowIndex = 0;
        else $this->rowIndex+=1;
        return $this;
    }
    /**
     * Adds a column
     *
     * @return HTMLForm
     */
    public function col():HTMLForm{
        if(!isset($this->colsIndex)) $this->colsIndex = 0;
        else $this->colsIndex+=1;
        return $this;
    }

    /**
     * Adds a text input
     *
     * @param string $name Inputs name
     * @param string $value Inputs value
     * @param string $class Inputs class
     * @param string $placeholder Inputs` placeholder
     * @param string $desc Description
     * @return HTMLForm
     */
    public function text(string $name, string $value='', string $class='', string $placeholder='', string $desc=''):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get($name).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="text"'.$class.$value.$placeholder.'/>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Adds a number input
     *
     * @param string $name Inputs name
     * @param integer $min Minimum number
     * @param integer $max Maximum number
     * @param integer $value Set number
     * @param string $class Inputs' class
     * @param string $placeholder Inputs password
     * @param string $desc Description
     * @return HTMLForm
     */
    public function number(string $name, int $min=0, int $max=5, int $value=0, string $class='', string $placeholder='', string $desc=''):HTMLForm{
        if($min>=$max)
            throw new NumberHandlingException((new Lang($this->lang[0],$this->lang[1]))->get('Errors','MinMaxError'));
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get($name).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="number" min="'.$min.'" max="'.$max.'"'.$class.$value.$placeholder.'/>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Adds a textarea
     *
     * @param string $name Inputs name
     * @param string $value Inputs value
     * @param string $class Inputs class
     * @param string $placeholder Inputs placeholder
     * @param string $desc Description
     * @return HTMLForm
     */
    public function textarea(string $name, string $value='', string $class='', string $placeholder='', string $desc=''):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get($name).'</label>
            <div class="input-group">
                <textarea name="'.$name.'" id="'.$name.'" type="text"'.$class.$placeholder.'>'.$value.'</textarea>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Adds a range input
     *
     * @param string $name Inputs name
     * @param integer $min Inputs minimum
     * @param integer $max Inputs maximum
     * @param integer $value Inputs value
     * @param integer $step Inputs step
     * @param string $class Inputs class
     * @param string $placeholder Inputs placeholder
     * @param string $desc Description
     * @return HTMLForm
     */
    public function range(string $name, int $min=0, int $max=5, int $value=0, int $step=1, string $class='', string $placeholder='', string $desc=''):HTMLForm{
        if($min>=$max)
            throw new NumberHandlingException((new Lang($this->lang[0],$this->lang[1]))->get('Errors','MinMaxError'));
        if($step<=0)
            throw new NumberHandlingException((new Lang($this->lang[0],$this->lang[1]))->get('Errors','RangeError'));
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get($name).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="range" step="'.$step.'" min="'.$min.'" max="'.$max.'"'.$class.$value.$placeholder.'/>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Adds a password input
     *
     * @param string $name Inputs name
     * @param string $value Inputs value
     * @param string $class Inputs class
     * @param string $placeholder Inputs password
     * @param string $desc Description
     * @return HTMLForm
     */
    public function password(string $name, string $value='', string $class='', string $placeholder='', string $desc=''):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get($name).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="password"'.$class.$value.$placeholder.'/>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }

    /**
     * Builds the form
     *
     * @param string $method Forms method **POST** or **GET**
     * @param string $action Forms action
     * @param string $enctype Forms encryption type
     * @return string Finalized form
     */
    public function finalize(string $method='post', string $action='', string $enctype='multipart/form-data'):string{
        $out = '<form method="'.$method.'"'.($action!=='' ? ' action="'.$action.'"' : '').($enctype!=='' ? ' enctype="'.$enctype.'"' : '').'>';
        foreach($this->elements as $rows){
            $out.='<div class="row">';
            foreach($rows as $cols) $out.='<div class="col">'.$cols.'</div>';
            $out.='</div>';
        }
        $out.='</form>';
        return $out;
    }
}
?>
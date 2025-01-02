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
    protected array $rowClass=[], $colClass=[];
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
     * @param string|null $class classname
     * @return HTMLForm
     */
    public function row(string|null $class=null):HTMLForm{
        if(!isset($this->rowIndex)) $this->rowIndex = 0;
        else $this->rowIndex+=1;
        $this->rowClass[$this->rowIndex] = $class;
        return $this;
    }
    /**
     * Adds a column
     *
     * @param string|null $class classname for columns
     * @return HTMLForm
     */
    public function col(string|null $class=null):HTMLForm{
        if(!isset($this->colsIndex)) $this->colsIndex = 0;
        else $this->colsIndex+=1;
        $this->colClass[$this->colsIndex] = $class;
        return $this;
    }
    /**
     * Gives a title for the form
     *
     * @param string $value Language location for the title
     * @return HTMLForm
     */
    public function title(string $value):HTMLForm{
        $this->elements[$this->rowIndex][$this->colsIndex] = '<h1 class="text-center">'.(new Lang($this->lang[0],$this->lang[1]))->get(...explode(',',$value)).'</h1>';
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
    public function text(string $name, string $value='', string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="text"'.$class.$value.$placeholder.($required ? ' required' : '').'/>
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
     * @param bool $required Requires the element
     * @return HTMLForm
     */
    public function number(string $name, int $min=0, int $max=5, int $value=0, string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        if($min>=$max)
            throw new NumberHandlingException((new Lang($this->lang[0],$this->lang[1]))->get('Errors','MinMaxError'));
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <input name="'.$name.'" id="'.$name.'" type="number" min="'.$min.'" max="'.$max.'"'.$class.$value.$placeholder.($required ? ' required' : '').'/>
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
     * @param bool $required Requires the input
     * @return HTMLForm
     */
    public function textarea(string $name, string $value='', string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <textarea name="'.$name.'" id="'.$name.'" type="text"'.$class.$placeholder.($required ? ' required' : '').'>'.$value.'</textarea>
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
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
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
     * @param bool $required Requires the input
     * @return HTMLForm
     */
    public function password(string $name, string $value='', string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <div class="psw_container">
                    <input name="'.$name.'" id="'.$name.'" type="password"'.$class.$value.$placeholder.($required ? ' required' : '').'/>
                    <i class="fa-solid fa-eye showPsw"></i>
                </div>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }

    /**
     * Adds a file input
     *
     * @param string $name Inputs name
     * @param string $value Inputs value
     * @param string $class Inputs class
     * @param string $placeholder Inputs password
     * @param string $desc Description
     * @param bool $required Requires the input
     * @return HTMLForm
     */
    public function file(string $name, string $value='', string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <input'.($required ? ' required': '').' name="'.$name.'" id="'.$name.'" type="file"'.$class.$value.$placeholder.'/>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Adds a file input
     *
     * @param string $name Inputs name
     * @param string $value Inputs value
     * @param string $class Inputs class
     * @param string $placeholder Inputs password
     * @param string $desc Description
     * @param bool $require requires the input
     * @return HTMLForm
     */
    public function color(string $name, string $value='', string $class='', string $placeholder='', string $desc='', bool $required=false):HTMLForm{
        $value = $value!=='' ? ' value="'.$value.'"' : '';
        $placeholder = $placeholder!=='' ? ' placeholder="'.$placeholder.'"' : '';
        $class = ' class="form-control'.($class!=='' ? ' '.$class : '').'"';
        $this->elements[$this->rowIndex][$this->colsIndex] = '<label class="form-label" for="'.$name.'">'.($required ? '<i class="fa-solid fa-asterisk" style="color: #ff0000;"></i> ' : '').(new Lang($this->lang[0],$this->lang[1]))->get('Inputs',str_replace('name=','',$name)).'</label>
            <div class="input-group">
                <input'.($required ? ' required' : '').' readonly name="'.$name.'" id="'.$name.'" type="text"'.$class.$value.$placeholder.'/>
                <div class="color-picker"></div>
            </div>
            <span class="form-text">'.(new Lang($this->lang[0],$this->lang[1]))->get(implode(',',explode(',',$desc))).'</span>';
        return $this;
    }
    /**
     * Generates a button
     *
     * @param string $name Buttons name
     * @param string $type Button type: **button**, **submit**, or **clear**
     * @param string $class Buttons class
     * @param string $link Buttons link
     * @return HTMLForm
     */
    public function button(string $name, string $type='button', string $class='', string $link=''):HTMLForm{
        $this->elements[$this->rowIndex][$this->colsIndex] = '<button'.($link!=='' ? ' btn-link="'.$link.'"' : '').' name="'.$name.'" id="'.$name.'" type="'.$type.'" class="btn'.($class!=='' ? ' '.$class : '').'">'.((new Lang($this->lang[0],$this->lang[1]))->get('Buttons',str_replace('name=','',$name))).'</button>';
        return $this;
    }
    /**
     * Loads google reCAPTCHA
     *
     * @param string $value Button label
     * @return HTMLForm
     */
    public function reCAPTCHA(string $value):HTMLForm{
        if(file_exists(NW_SQL_CREDENTIALS)){
            $sql = new SSQL();
            $c = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            if($sql->setCredential($c['server'],$c['user'],$c['psw'])){
                $db = $sql->selectDB($c['db']);
                $captcha = $db->selectData('recaptcha',['*']);
                if($captcha[0]['reCAPTCHA_active']){
                    if(strtolower($captcha[0]['reCAPTCHA_version'])==='v3')
                        $this->elements[$this->rowIndex][$this->colsIndex] = '<button class="g-recaptcha" data-sitekey="'.$captcha[0]['reCAPTCHA_siteKey'].'" data-callback="gSubmit" data-expired-callback="gExpire" data-action="submit">'.((new Lang($this->lang[0],$this->lang[1]))->get('Inputs',$value)).'</button>';
                    else{
                        if(strtolower($captcha[0]['reCAPTCHA_type'])==='invisible')
                            $this->elements[$this->rowIndex][$this->colsIndex] = '<button class="g-recaptcha" data-sitekey="'.$captcha[0]['reCAPTCHA_siteKey'].'" data-callback="gSubmit" data-expired-callback="gExpire">'.((new Lang($this->lang[0],$this->lang[1]))->get('Inputs',$value)).'</button>';
                        else
                            $this->elements[$this->rowIndex][$this->colsIndex] = '<div class="g-recaptcha my-3" required data-sitekey="'.$captcha[0]['reCAPTCHA_siteKey'].'" data-callback="gSubmit" data-expired-callback="gExpire"></div>';
                    }
                    return $this;
                } else {
                    return $this;
                }
            }
            return $this;
        }else return $this;
    }

    /**
     * Builds the form
     *
     * @param string $method Forms method **POST** or **GET**
     * @param string $action Forms action
     * @param string $enctype Forms encryption type
     * @param string $class Class name for the form
     * @return string Finalized form
     */
    public function finalize(string $method='post', string $action='', string $enctype='', string $class=''):string{
        $out = '<form'.($class!=='' ? ' class="'.$class.'"' : '').' method="'.$method.'"'.($action!=='' ? ' action="'.$action.'"' : '').($enctype!=='' ? ' enctype="'.$enctype.'"' : '').' novalidate>';
        $out.='<div class="text-bg-danger w-100 p-3 rounded text-center fs-2 d-none" class="form_error">
            <i class="fa-solid fa-triangle-exclamation"></i> <span class="errmsg"></span>
        </div>';
        $index = [0,0];
        foreach($this->elements as $rows){
            $out.='<div class="row'.(trim($this->rowClass[$index[0]])!=='' ? ' '.$this->rowClass[$index[0]] : '').'">';
            foreach($rows as $cols) {
                $out.='<div class="col'.(trim($this->colClass[$index[1]])!=='' ? ' '.$this->colClass[$index[1]] : '').'">'.$cols.'</div>';
                $index[1]+=1;
            }
            $out.='</div>';
            $index[0]+=1;
        }
        $out.='</form>';
        return $out;
    }
}
?>
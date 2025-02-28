<?php
namespace NetWorks\libs;
include_once dirname(path: __DIR__).'/init.php';
use NetWorks\libs\CSRF;
use Exception;
class HTMLForm{
    protected string $required;
    protected array $langs;
    protected CSRF $token;
    public function __construct() {
        global $lang;
        $this->required = ' <i class="material-symbols-rounded text-danger">asterisk</i>';
        $this->langs = $lang;
        $this->token = new CSRF();
    }
    /**
     * Cleans the input
     * @param string $text String to clean
     * @throws \Exception Invalid charset
     * @return string Sanitized string
     */
    public function clean(string $text): string{
        if (!defined(constant_name: 'CHARSET')) {	
            throw new Exception(message: 'CHARSET is not defined.');	
        }		
        return htmlspecialchars(string: trim(string: $text ?? ''), flags: ENT_QUOTES, encoding: CHARSET);	
    } 
    /**
     * Translates Newlines
     * @param string $text Text to remove translate newlines
     * @return array|string|null Fixed string
     */
    public function transNL(string $text): array|string|null{
        $normalizedText = str_replace(search: ["\r\n", "\r"], replace: "\n", subject: $text);
        return preg_replace(pattern: '/\n{3,}/', replacement: "\n\n", subject: $normalizedText);
	}
    /**
     * Creates and HTML form
     * @param string $controls Form controls (buttons, textbox, etc...)
     * @param string $method POST/GET
     * @param string $action Location to send the form request
     * @param string $class Extra class attributes
     * @param string $enctype Encoding type
     * @return string Form results
     */
    public function form(string $controls, string $method='POST', string $action='', string $class='', string $enctype='multipart/form-data', array $attr=[]):string{
        if(strtolower(string: $method)!=='post'&&strtolower(string: $method)!=='get') die($this->langs['invalidFormMethod']);
        $class = $class ? " class=\"".htmlspecialchars(string: $class)." needs-validation\"" : ' class="needs-validation"';
        $enctype = $enctype ? " enctype=\"".htmlspecialchars(string: $enctype)."\"" : '';
        $action = $action ? " action=\"".htmlspecialchars(string: $action)."\"" : '';
        $attr = !empty($attr) ? array_map(callback: function($e) use ($attr): string{
            return "$e=\"".$attr[$e].'"';
        },array: array_keys($attr)) : '';
        return "<form method=\"".htmlspecialchars(string: strtolower(string: $method))."\"{$action}{$enctype}{$class}".(!empty($attr) ? implode(separator: ' ',array: $attr) : '')." novalidate>
            <input type=\"hidden\" name=\"_token\" value=\"".$this->token->createToken()->getToken()."\"/>
            $controls
        </form>";
    }
    /**
     * Adds a textbox
     * @param string $name Textbox`s name
     * @param string $default Default value
     * @param string $type Type of input
     * @param string $icon Icon
     * @param string $class Classlist
     * @param string $placeholder Placeholder
     * @param string $desc Description
     * @param bool $disabled Disabled
     * @param bool $required Required
     * @return string Text input
     */
    public function text(string $name, string $default='', string $type='text', string $icon='', string $class='', string $placeholder='',string $desc='',bool $disabled=false, bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $placeholderAttr = !empty($placeholder) ? " placeholder=\"".($this->langs[$placeholder]??'')."\"" : '';
        $disabledAttr = $disabled ? " disabled=\"disabled\"" : '';
        $requiredAttr = $required ? ' required="true"' : '';
        $icon = $icon ? "<i class=\"material-symbols-rounded\">".htmlspecialchars(string: $icon)."</i> ": '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">$icon".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"".htmlspecialchars(string: $type)."\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$placeholderAttr}{$disabledAttr}{$requiredAttr}/>
        $descHtml";
    }
    /**
     * Summary of password
     * @param string $name Passwords name
     * @param string $default Passwords default
     * @param string $icon Icon
     * @param string $class Classlist
     * @param string $placeholder Placeholder
     * @param string $desc Description
     * @param bool $required Required
     * @param bool $checklist Password check list
     * @return string Password input
     */
    public function password(string $name, string $default = '', string $icon='', string $class = '', string $placeholder = '', string $desc = '', bool $required=false, bool $checklist=false):string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $placeholderAttr = !empty($placeholder) ? " placeholder=\"".($this->langs[$placeholder]??'')."\"" : '';
        $requiredAttr = $required ? ' required="true"' : '';
        $icon = $icon ? "<i class=\"material-symbols-rounded\">".htmlspecialchars(string: $icon)."</i> ": '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $checkList = $checklist ? '<div class="checklist">
            <ul class="list-group list-group-flush mt-2 rounded">
                <li class="list-group-item list-group-item-danger"><i class="material-symbols-rounded text-danger">close</i> '.$this->langs['psw_validation_8_chars'].'</li>
                <li class="list-group-item list-group-item-danger"><i class="material-symbols-rounded text-danger">close</i> '.$this->langs['psw_validation_uppercase'].'</li>
                <li class="list-group-item list-group-item-danger"><i class="material-symbols-rounded text-danger">close</i> '.$this->langs['psw_validation_lowercase'].'</li>
                <li class="list-group-item list-group-item-danger"><i class="material-symbols-rounded text-danger">close</i> '.$this->langs['psw_validation_numbers'].'</li>
                <li class="list-group-item list-group-item-danger"><i class="material-symbols-rounded text-danger">close</i> '.$this->langs['psw_validation_special_chars'].'</li>
            </ul>
        </div>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">$icon".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <div class=\"psw-container\">
            <input type=\"password\"".($checkList ? ' data-checklist="true"' : '')." id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$placeholderAttr}{$requiredAttr}/>
            <div class=\"material-symbols-rounded toggle-psw-visible\">visibility</div>
        </div>
        $descHtml
        $checkList";
    }
    /**
     * A simple submit button
     * @param string $name Button name
     * @param string $button Button type. _button or submit_
     * @param string $link Link
     * @param string $class Classlist
     * @param string $icon Icon
     * @param bool $cancel Include reset button
     * @return string Submit button
     */
    public function button(string $name, string $button='button', string $link='',string $class = '', string $icon = '', bool $cancel = false):string{
        $classAttr = !empty($class) ? ' class="' . htmlspecialchars(string: $class) . '"' : ' class="btn btn-primary"';
        $iconHtml = !empty($icon) ? '<i class="' . htmlspecialchars(string: $icon) . '"></i>&nbsp;' : '';
        $cancelButton = $cancel ? '&nbsp;<button type="reset" class="btn btn-secondary" onclick="$(\'#form\').remove();"><i class="material-symbols-outlined">close</i>&nbsp;' . htmlspecialchars(string: $this->langs['cancel']) . '</button>' : '';
        if($link)
            return '<a target="_self" href="'.filter_var(value: $link,filter: FILTER_SANITIZE_URL).'"><button' . $classAttr . ' type="'.htmlspecialchars(string: $button).'" name="'.htmlspecialchars(string: $name).'">' . $iconHtml . htmlspecialchars(string: ($this->langs[$name]??'')) . '</button></a>' . $cancelButton;
        else
            return '<button' . $classAttr . ' type="'.htmlspecialchars(string: $button).'" name="'.htmlspecialchars(string: $name).'">' . $iconHtml . htmlspecialchars(string: ($this->langs[$name]??'')) . '</button>' . $cancelButton;
    }
    /**
     * Generates a select box
     * @param string $name Name
     * @param string|array{value: string, name: string} $options Options
     * @param string $default Default value
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string Select box
     */
    public function select(string $name, string|array $options, string $default='', string $class='', string $desc='', bool $required=false): string{
        if(!is_array(value: $options)){
            $options = explode(separator: ',',string: $options);
        }
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-select ".htmlspecialchars(string: $class)."\"" : ' class="form-select"';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $out = "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">".($this->langs[$name]??'')."</label>
        <select id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\"{$classAttr}{$requiredAttr}>
            ";
        
        foreach($options as $key => $label){
            if(is_int(value: $key)){
                $key = $label;
            }
            $out.="<option value=\"".htmlspecialchars(string: $key)."\"".($key===$default ? " selected=\"selected\"" : "").">".htmlspecialchars(string: $label)."</option>";
        }
        $out.="</select>
        $descHtml";
        return $out;
    }
    /**
     * Adds radio buttons
     * @param string $name Radio name
     * @param string $default Default
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string
     */
    public function radio(string $name, string $default='', string $class='', string $desc='', bool $required=false, bool $disabled=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-check-input ".htmlspecialchars(string: $class)."\"" : ' class="form-check-input"';
        $requiredAttr = $required ? ' required="true"' : '';
        $disabledAttr = $disabled ? " disabled=\"disabled\"" : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $out = "<div class=\"form-check\">
            <label for=\"".htmlspecialchars(string: $name)."\">".($this->langs[$name]??'')."</label>
            <input type=\"radio\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".($this->langs[$name]??'')."\"".($value===$default&&$value ? " checked=\"checked\"" : "")."{$classAttr}{$requiredAttr}{$disabledAttr}/> 
        </div>
        $descHtml";
        return $out;
    }
    /**
     * Adds checkbox
     * @param string $name Name
     * @param string $default Default
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @param bool $disabled Disabled
     * @return string Checkbox input
     */
    public function checkBox(string $name,  string $default='', string $class='', string $desc='', bool $required=false, bool $disabled=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-check-input ".htmlspecialchars(string: $class)."\"" : ' class="form-check-input"';
        $requiredAttr = $required ? ' required="true"' : '';
        $disabledAttr = $disabled ? " disabled=\"disabled\"" : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $out = "<div class=\"form-check\">
            <label for=\"".htmlspecialchars(string: $name)."\">".($this->langs[$name]??'').($required ? $this->required : '')."</label>
            <input type=\"checkbox\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"".($value===$default&&$value ? " checked=\"checked\"" : "")."{$classAttr}{$requiredAttr}{$disabledAttr}/> 
        </div>
        $descHtml";
        return $out;
    }
    /**
     * Adds a textarea
     * @param string $name Name
     * @param string $default Default
     * @param string $class Classlist
     * @param string $placeholder Placeholder
     * @param string $desc Description
     * @param bool $required Required
     * @return string Textarea input
     */
    public function textarea(string $name, string $default='', string $class='', string $placeholder='', string $desc='', bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $placeholderAttr = !empty($placeholder) ? " placeholder=\"".($this->langs[$placeholder]??'')."\"" : '';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <textarea id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\"{$classAttr}{$placeholderAttr}{$requiredAttr}>".htmlspecialchars(string: $value)."</textarea>
        $descHtml";
    }
    /**
     * Adds a file input
     * @param string $name Name
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string File input
     */
    public function file(string $name, string $class='', string $desc='', bool $required=false, string $allowed=''): string{
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $allowedAttr = !empty($allowed) ? " accept=\"".htmlspecialchars(string: $allowed)."\"" : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"file\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\"{$classAttr}{$requiredAttr}{$allowedAttr}/>
        $descHtml";
    }
    /**
     * Adds a hidden input
     * @param string $name Name
     * @param string $value Value
     * @return string Hidden input
     */
    public function hidden(string $name, string $value): string{
        return "<input type=\"hidden\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"/>";
    }
    /**
     * Color input
     * @param string $name Name
     * @param string $default Default
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string Color input
     */
    public function color(string $name, string $default='', string $class='', string $desc='', bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"color\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$requiredAttr}/>
        $descHtml";
    }
    /**
     * Adds a range input
     * @param string $name Name
     * @param string $default Default
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string Range input
     */
    public function range(string $name, string $default='', string $class='', string $desc='', bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"range\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$requiredAttr}/>
        $descHtml";
    }
    /**
     * Creates a date input
     * @param string $name Name
     * @param string $default Value
     * @param string $type _date_, _datetime-local_, _month_, _week_, or _time_
     * @param string $icon Icon
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string Date input
     */
    public function date(string $name, string $default='', string $type='date', string $icon='', string $class='', string $desc='', bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $requiredAttr = $required ? ' required="true"' : '';
        $icon = $icon ? "<i class=\"material-symbols-rounded\">".htmlspecialchars(string: $icon)."</i> ": '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">$icon".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"".htmlspecialchars(string: $type)."\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$requiredAttr}/>
        $descHtml";
    }
    /**
     * Adds a number input
     * @param string $name Name
     * @param string $default Default
     * @param string $icon Icon
     * @param string $class Classlist
     * @param string $desc Description
     * @param bool $required Required
     * @return string Number input
     */
    public function number(string $name, string $default='', string $icon='', string $class='', string $desc='', bool $required=false): string{
        $value = (new Utils())->isPOST(element: $name) ? $this->clean(text: $_POST[$name]) : $default;
        $classAttr = !empty($class) ? " class=\"form-control ".htmlspecialchars(string: $class)."\"" : ' class="form-control"';
        $requiredAttr = $required ? ' required="true"' : '';
        $descHtml = !empty($desc) ? '<span class="text-muted">' . ($this->langs[$desc]??'') . '</span>' : '';
        $icon = $icon ? "<i class=\"material-symbols-rounded\">".htmlspecialchars(string: $icon)."</i> ": '';
        return "<label for=\"".htmlspecialchars(string: $name)."\" class=\"form-label\">$icon".($this->langs[$name]??'').($required ? $this->required : '')."</label>
        <input type=\"number\" id=\"".htmlspecialchars(string: $name)."\" name=\"".htmlspecialchars(string: $name)."\" value=\"".htmlspecialchars(string: $value)."\"{$classAttr}{$requiredAttr}/>
        $descHtml";
    }
    /**
     * Adds a label
     * @param string $name Name for the label
     * @param string $icon Icon
     * @param string $class Classlist
     * @param bool $required Required
     * @return string Label element
     */
    public function label(string $name, string $icon='', string $class='', bool $required=false): string{
        $classAttr = !empty($class) ? " class=\"form-label ".htmlspecialchars(string: $class)."\"" : ' class="form-label"';
        $icon = $icon ? "<i class=\"material-symbols-rounded\">".htmlspecialchars(string: $icon)."</i> ": '';
        return "<label for=\"".htmlspecialchars(string: $name)."\"{$classAttr}>$icon".($this->langs[$name]??'').($required ? $this->required : '')."</label>";
    }
}
?>
<?php
include_once 'init.php';
include_once 'header.php';
include_once 'footer.php';
use NetWorks\libs\HTMLForm;
use NetWorks\libs\Utils;
use NetWorks\libs\Plugins;
global $lang;
$form = new HTMLForm();
$utils = new Utils();
if(file_exists(filename: 'installed.bin.key')) header(header: 'Location: ./');
$template = '
<div class="row">
    <div class="col">
        '.$form->text(name: 'fname',required: true).'
        <div class="invalid-msg">'.$lang['requiredFname'].'</div>
    </div>
    <div class="col">
        '.$form->text(name: 'mname').'
    </div>
    <div class="col">
        '.$form->text(name: 'lname',required: true).'
        <div class="invalid-msg">'.$lang['requiredLname'].'</div>
    </div>
</div>
<div class="row">
    <div class="col">
        '.$form->text(name: 'username', required: true).'
        <div class="invalid-msg">'.$lang['requiredUsername'].'</div>
    </div>
    <div class="col">
        '.$form->text(name: 'email', type: 'email', required: true).'
        <div class="invalid-msg">'.$lang['requiredEmail'].'</div>
    </div>
</div>
<div class="row">
    <div class="col">
        '.$form->password(name: 'password', required: true, checklist: true).'
        <div class="invalid-msg">'.$lang['requiredPsw'].'</div>
    </div>
    <div class="col">
        '.$form->password(name: 'confirm_password', required: true).'
        <div class="invalid-msg">'.$lang['requiredConfirmPsw'].'</div>
    </div>
</div>
<div class="row">
    <div class="col">
        '.$form->select(name: 'lang', options: $utils->getLang()).'
    </div>
    <div class="col">
        '.$form->select(name: 'timezone', options: timezone_identifiers_list(),default: 'America/New_York',desc: 'noInstallChange').'
    </div>
</div>
<div class="row">
    <div class="col">
        '.$form->button(name: 'submit',button: 'submit',class: 'btn btn-success w-100 mt-2').'
    </div>
</div>';
echo "$header
<div class=\"installation\">
    <h1 class=\"text-center text-capitalize\">".$lang['projectName'].' - '.$lang['install']."</h1>
    ".$form->form(controls: $template)."
</div>
$footer";
?>
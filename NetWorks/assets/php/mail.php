<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
use networks\libs\Web;
use networks\libs\Utils;

require_once dirname(__DIR__,2).'/init.php';

if(isset($_REQUEST['type'])){
    switch(strtolower($_REQUEST['type'])){
        case 'verify':
            if((new Utils())->checkMail())
                (new Utils())->sendMail('verify','noreply@networks.com',[$_REQUEST['name']=>$_REQUEST['email']],(new Lang())->get(['mail','verify','subject']),(new Lang())->get(['mail','verify','body']).'<br/><a href="'.(new Web(NW_ROOT.'/verify?user='.$_REQUEST['username']))->toAccessable().'">'.(new Lang())->get(['mail','verify','label']).'</a>');
        break;
    }
}
?>
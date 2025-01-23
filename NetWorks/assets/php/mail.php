<?php
header('Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
use networks\libs\Web;
use networks\libs\Utils;

require_once dirname(__DIR__,2).'/init.php';

if(isset($_REQUEST['type'])){
    switch(strtolower($_REQUEST['type'])){
        case 'verify':
            if((new Utils())->checkMail()){
                if((new Utils())->sendMail('verify','noreply@networks.com',[$_REQUEST['name']=>$_REQUEST['email']],(new Lang())->get(['mail','verify','subject']),(new Lang())->get(['mail','verify','body']).'<br/><a href="'.(new Web(NW_ROOT.'/verify?user='.$_REQUEST['name']))->toAccessible().'">'.(new Lang())->get(['mail','verify','label']).'</a>')){
                    echo json_encode(['success'=>true],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                }else echo json_encode(['err'=>'fail to send'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            }else echo json_encode(['err'=>'Mail is not available'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        break;
    }
}
?>
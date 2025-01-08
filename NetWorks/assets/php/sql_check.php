<?php
header(header: 'Content-Type: application/json; charset=utf-8');
use networks\libs\Lang;
use networks\libs\Web;
require_once(dirname(__DIR__,2).'/libs/ssql.lib.php');
require_once(dirname(__DIR__,2).'/libs/lang.lib.php');
require_once(dirname(__DIR__,2).'/init.php');
$sql = new SSQL();
if($sql->setCredential($_REQUEST['server'],$_REQUEST['user'],$_REQUEST['psw'])){
    if($sql->checkDB($_REQUEST['db'])){
        # Create tables
        $db = $sql->selectDB($_REQUEST['db']);
        $db->import(NW_ASSETS.NW_DS.'sql'.NW_DS.'install.sql');
        
        $db->addData('config',['title',
    'ico16',
    'ico24',
    'ico32',
    'ico48',
    'ico64',
    'ico96',
    'ico128',
    'ico256',
    'ico512'],
    ['NetWorks',
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon16.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon24.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon32.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon48.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon64.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon96.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon128.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon256.ico'))->toAccessible(),
    (new Web(NW_ASSETS.NW_DS.'icons'.NW_DS.'icon512.ico'))->toAccessible(),
    ]);
        $db->addData('pages',[
            'pageName',
            'pageIcon',
            'pageDraft'
        ],[
            'home',
            'fa-solid fa-house',
        (new Web(NW_DRAFTS.NW_DS.'home.html'))->toAccessible()
        ]);
        $db->addData('pages',[
            'pageName',
            'pageIcon',
            'pageDraft'
        ],[
            'forum',
            'fa-solid fa-comments',
            (new Web(NW_DRAFTS.NW_DS.'forum.html'))->toAccessible()
        ]);
        $db->addData('reCAPTCHA',[
            'reCAPTCHA_active',
            'reCAPTCHA_version',
            'reCAPTCHA_siteKey',
            'reCAPTCHA_secretKey'
        ],[
            0,
            'v2',
            '',
            ''
        ]);
        echo json_encode(['success'=>true],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $c = fopen(NW_SQL_CREDENTIALS,'w+');
        fwrite($c,json_encode(['server'=>$_REQUEST['server'],'user'=>$_REQUEST['user'],'psw'=>$_REQUEST['psw'],'db'=>$_REQUEST['db']],JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        fclose($c);
        $sql->close();
    }else{
        echo json_encode(['err'=>(new Lang())->get('Errors','noSQLDB')],JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        $sql->close();
    }
}else
    echo json_encode(['err'=>(new Lang())->get('Errors','noSQLCred')],JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
?>
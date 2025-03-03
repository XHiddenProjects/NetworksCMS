<?php
use NetWorks\libs\Database;
use NetWorks\libs\Plugins;
header(header: 'Content-Type: application/json; charset=utf-8');
include_once dirname(path: __DIR__).'/init.php';
use PHPMailer\PHPMailer\PHPMailer;
use NetWorks\libs\Utils;
use NetWorks\libs\CSRF;
use NetWorks\libs\Users;
$utils = new Utils();
$csrf = new CSRF();
$user = new Users();
$plugin = new Plugins();
global $lang;   
if($utils->isREQUEST(element: 'install')){
    if(!$csrf->validateToken(token: htmlspecialchars(string: $_REQUEST['token']))){
        echo json_encode(value: ['token_valid'=>$csrf->validateToken(token: htmlspecialchars(string: $_REQUEST['token']))],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }else{
        date_default_timezone_set(timezoneId: htmlspecialchars(string: $_REQUEST['timezone']));
        $db = new Database(file: 'NetworksCMS',flags: SQLITE3_OPEN_READWRITE|SQLITE3_OPEN_CREATE, key: htmlspecialchars(string: $_REQUEST['psw']));
        $db->dropTable(name: 'users');
        $db->createTable(name: 'users',options: [
            'user_id'=>'INTEGER PRIMARY KEY AUTOINCREMENT',
            'fname'=>'VARCHAR(50) NOT NULL',
            'mname'=>'VARCHAR(50) NULL DEFAULT ""',
            'lname'=>'VARCHAR(800) NOT NULL',
            'username'=>'VARCHAR(120) NOT NULL',
            'email'=>'VARCHAR(255) NOT NULL',
            'psw'=>'VARCHAR(150) NOT NULL',
            'joined'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'IP'=>'VARCHAR(15) NOT NULL',
            'browser'=>'VARCHAR(100) NOT NULL',
            'os'=>'VARCHAR(80) NOT NULL',
            'type'=>'VARCHAR(200) NOT NULL',
            'confirmed'=>'TINYINT(1) NOT NULL DEFAULT 0'
        ]);
        $db->selectTable(name: 'users')->insert(name: null,data: [
            'fname'=>htmlspecialchars(string: $_REQUEST['fname']),
            'mname'=>htmlspecialchars(string: $_REQUEST['mname']),
            'lname'=>htmlspecialchars(string: $_REQUEST['lname']),
            'username'=>htmlspecialchars(string: $_REQUEST['username']),
            'email'=>filter_var(value: filter_var(value: $_REQUEST['email'], filter: FILTER_VALIDATE_EMAIL),filter: FILTER_SANITIZE_EMAIL),
            'psw'=>password_hash(password: $_REQUEST['psw'],algo: PASSWORD_DEFAULT),
            'IP'=>$user->ip(),
            'Browser'=>$user->getDevice(select: 'browser_title'),
            'OS'=>$user->getDevice(select: 'os_title'),
            'type'=>'admin',
            'confirmed'=>1
        ]);
        $db->createTable(name: 'online',options: [
            'username'=>'VARCHAR(120) NOT NULL',
            'status'=>'TINYINT(1) DEFAULT 0',
            'last_checked'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP'
        ]);
        $db->selectTable(name: 'online')->insert(name: null,data: [
            'username'=>htmlspecialchars(string: $_REQUEST['username']),
            'status'=>0,
            'last_checked'=>date(format: 'Y-m-d H:i:s')
        ]);
        $db->createTable(name: 'plugins',options: [
            'plugin_name'=>'VARCHAR(250) NOT NULL',
            'plugin_status'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'plugin_disabled'=>'TINYINT(1) NOT NULL DEFAULT 0'
        ]);
        $db->createTable(name: 'settings',options: [
            'title'=>'VARCHAR(180) NOT NULL',
            'description'=>'VARCHAR(180) NOT NULL',
            'author'=>'VARCHAR(80) NOT NULL',
            'charset'=>'VARCHAR(40) NOT NULL DEFAULT "'.CHARSET.'"',
            'theme'=>'VARCHAR(100) NOT NULL DEFAULT "default"',
            'language'=>'VARCHAR(5) NOT NULL DEFAULT "en-us"',
            'timezone'=>'VARCHAR(40) NOT NULL DEFAULT "America/New_York"'
        ]);
        $db->selectTable(name: 'settings')->insert(name: null, data: [
            'title'=>$lang['projectName'],
            'description'=>$lang['description'],
            'author'=>$lang['author'],
            'timezone'=>htmlspecialchars(string: $_REQUEST['timezone'])
        ]);
        $db->createTable(name: 'mail',options: [
            'host'=>'VARCHAR(255) NOT NULL DEFAULT ""',
            'SMTPAuth'=>'TINYINT(1) NOT NULL DEFAULT 1',
            'auth_username'=>'VARCHAR(255) NOT NULL DEFAULT ""',
            'auth_psw'=>'TEXT NULL DEFAULT ""',
            'SMTPSecure'=>'VARCHAR(3) NOT NULL DEFAULT "'.PHPMailer::ENCRYPTION_STARTTLS.'"',
            'port'=>'TINYINT(3) NOT NULL DEFAULT 587'
        ]);
        $db->selectTable(name: 'mail')->insert(name: null,data: [
            'host'=>'',
            'SMTPAuth'=>1,
            'auth_username'=>'',
            'auth_psw'=>'',
            'SMTPSecure'=>PHPMailer::ENCRYPTION_STARTTLS,
            'port'=>587
        ]);
        $db->createTable(name: 'permissions',options: [
            'label'=>'VARCHAR(180) NOT NULL',
            # forum
            'canPost'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canReply'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canForum'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canBan'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canReport'=>'TINYINT(1) NOT NULL DEFAULT 0',
            #Dashboard
            'canConfig'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canFileManage'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canPlugin'=>'TINYINT(1) NOT NULL DEFAULT 0',
            #CMS
            'canBuild'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canDelete'=>'TINYINT(1) NOT NULL DEFAULT 0',
            'canView'=>'TINYINT(1) NOT NULL DEFAULT 0',
        ]);
        $permsTable = $db->selectTable(name: 'permissions');
        $permsTable->insert(name: null,data: [
            'label'=>'admin',
            'canPost'=>1,
            'canReply'=>1,
            'canForum'=>1,
            'canBan'=>1,
            'canReport'=>1,
            'canConfig'=>1,
            'canFileManage'=>1,
            'canPlugin'=>1,
            'canBuild'=>1,
            'canDelete'=>1,
            'canView'=>1
        ]);
        $permsTable->insert(name: null,data: [
            'label'=>'moderator',
            'canPost'=>1,
            'canReply'=>1,
            'canForum'=>1,
            'canBan'=>1,
            'canReport'=>1,
            'canConfig'=>0,
            'canFileManage'=>0,
            'canPlugin'=>0,
            'canBuild'=>0,
            'canDelete'=>0,
            'canView'=>1
        ]);
        $permsTable->insert(name: null,data: [
            'label'=>'member',
            'canPost'=>1,
            'canReply'=>1,
            'canForum'=>0,
            'canBan'=>0,
            'canReport'=>1,
            'canConfig'=>0,
            'canFileManage'=>0,
            'canPlugin'=>0,
            'canBuild'=>0,
            'canDelete'=>0,
            'canView'=>1
        ]);
        $permsTable->insert(name: null,data: [
            'label'=>'banned',
            'canPost'=>0,
            'canReply'=>0,
            'canForum'=>0,
            'canBan'=>0,
            'canReport'=>0,
            'canConfig'=>0,
            'canFileManage'=>0,
            'canPlugin'=>0,
            'canBuild'=>0,
            'canDelete'=>0,
            'canView'=>1
        ]);
        $db->createTable(name: 'api',options: [
            'key'=>'VARCHAR(50) NOT NULL',
            'permissions'=>'TEXT NOT NULL',
            'expires'=>'DATETIME NOT NULL'
        ]);
        $db->selectTable(name: 'api')->insert(name: null,data: [
            'key'=>$utils->generateAPI(),
            'permissions'=>'"users,forums,replies,topics"',
            'expires'=>date(format: 'Y-m-d H:i:s',timestamp: strtotime(datetime: '+500 years'))
        ]);
        $db->createTable(name: 'forums',options: [
            'title'=>'VARCHAR(120) NOT NULL UNIQUE',
            'created'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'edited'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'color'=>'VARCHAR(6) NOT NULL DEFAULT "#FFFFFF"',
            'icon'=>'VARCHAR(200) NOT NULL DEFAULT "forum"'
        ]);
        $db->createTable(name: 'topics',options: [
            'title'=>'VARCHAR(120) NOT NULL UNIQUE',
            'body'=>'TEXT NOT NULL',
            'author'=>'VARCHAR(120) NOT NULL',
            'created'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'edited'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'forum'=>'VARCHAR(120) NOT NULL'
        ]);
        $db->createTable(name: 'replies',options: [
            'body'=>'TEXT NOT NULL',
            'author'=>'VARCHAR(120) NOT NULL',
            'created'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'edited'=>'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'topic'=>'VARCHAR(120) NOT NULL'
        ]);
        $db->close();
        $plugin->hook(hookName: 'install');
        $installKey = fopen(filename: dirname(path: __DIR__).'/installed.bin.key', mode: 'w+');
        fwrite(stream: $installKey,data: hash(algo: 'sha512', data: uniqid(prefix: rand(), more_entropy: true),binary: true));
        fclose(stream: $installKey);
        echo json_encode(value: ['success'=>true],flags: JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}
?>
<?php
namespace NetWorks\libs;
/**
 * Utilities library
 */
include_once dirname(__DIR__).'/init.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use NetWorks\libs\Database;
use NetWorks\libs\Files;
class Utils{
    public function __construct() {
        
    }
    /**
     * Check for a POST header
     * @param string $element header to get from post
     * @return bool TRUE if header has been posted, else FALSE
     */
    public function isPOST(string $element): bool{
        return isset($_POST[$element]) ? true : false;
    }
    /**
     * Check for GET header
     * @param string $element header to element
     * @return bool TRUE if header has been got, else FALSE
     */
    public function isGET(string $element): bool{
        return isset($_GET[$element]) ? true : false;
    }
    /**
     * Checks for REQUEST header
     * @param string $element header to element
     * @return bool TRUE if header has been got, else FALSE
     */
    public function isREQUEST(string $element): bool{
        return isset($_REQUEST[$element]) ? true : false;
    }
    /**
     * Returns the list of languages
     * @return array
     */
    public function getLang(): array{
        $files = new Files();
        $languages = [];
        foreach($files->scan(dir: NW_LANGUAGES) as $files){
            include_once NW_LANGUAGES.NW_DS.$files;
            global $lang;
            $name = preg_replace(pattern: '/\.php/',replacement: '',subject: $files);
            $languages[$name] = $lang['name'];
        }
        return $languages;
    }
    /**
     * Sends e-mail
     * @param array $from Email to sender it from: [email=>name] **Use: '' to have no name**
     * @param string|array $to Email to send it to: [email=>name...] **Use: '' to have no name**
     * @param string $subject Subject line
     * @param string $body Message
     * @param string $altBody Alt. body
     * @param string|array $cc Include carbon-copy: [email=>name...] **Use: '' to have no name**
     * @param string|array $bcc Include bind carbon-copy: [email=>name...] **Use: '' to have no name**
     * @param array|string $attachments Include attachments: [filepath=>filename...] **Use: '' to have no name**
     * @param bool $isHTML Is this message in HTML?
     * @throws \PHPMailer\PHPMailer\Exception
     * @return bool TRUE if the message was sent, else FALSE
     */
    public function sendMail(array $from, string|array $to, string $subject, string $body, string $altBody='', string|array $cc=[], string|array $bcc=[], array|string $attachments=[], bool $isHTML=true): bool{
        $mail = new PHPMailer(exceptions: true);
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $mailDB = $db->selectTable(name: 'mail');
        $failedSend = true;
        try{
            global $lang;
            $mail->SMTPDebug = true;
            $mail->isSMTP();
            $mail->Host = $mailDB->select(selector: 'host');
            $mail->SMTPAuth = $mailDB->select(selector: 'SMTPAuth');
            $mail->Username = $mailDB->select(selector: 'auth_username');
            $mail->Password = $mailDB->select(selector: 'auth_psw');
            $mail->SMTPSecure = $mailDB->select(selector: 'SMTPSecure');
            $mail->Port = $mailDB->select(selector: 'port');
            if(count(value: $from)>1||count(value: $from)==0) throw new Exception(message: $lang['errMailFrom']);
            else{
                foreach($from as $email=>$name){
                    if(isset($from[$email]) && $from[$email] ?? null !== null) throw new Exception(message: $lang['errMailFormat']);
                    $email = filter_var(value: filter_var(value: $email,filter: FILTER_SANITIZE_EMAIL),filter: FILTER_VALIDATE_EMAIL);
                    $mail->setFrom(address: $email,name: $name??'');
                }
            }
            switch (count(value: $to)) {
                case 0:
                    throw new Exception(message: $lang['errMailTo']);
                default:
                    foreach ($to as $email => $name) {
                        if(isset($to[$email]) && $to[$email] ?? null !== null) throw new Exception(message: $lang['errMailFormat']);
                        $email = filter_var(value: filter_var(value: $email,filter: FILTER_SANITIZE_EMAIL),filter: FILTER_VALIDATE_EMAIL);
                        $mail->addAddress(address: $email,name: $name);
                    }
                break;
            }

            if(!empty($cc)){
                foreach($cc as $email=>$name){
                    if(isset($cc[$email]) && $cc[$email] ?? null !== null) throw new Exception(message: $lang['errMailFormat']);
                    $email = filter_var(value: filter_var(value: $email,filter: FILTER_SANITIZE_EMAIL),filter: FILTER_VALIDATE_EMAIL);
                    $mail->addCC(address: $email,name: $name);
                }
            }
            if(!empty($bcc)){
                foreach($bcc as $email=>$name){
                    if(isset($bcc[$email]) && $bcc[$email] ?? null !== null) throw new Exception(message: $lang['errMailFormat']);
                    $email = filter_var(value: filter_var(value: $email,filter: FILTER_SANITIZE_EMAIL),filter: FILTER_VALIDATE_EMAIL);
                    $mail->addBCC(address: $email,name: $name);
                }
            }

            if(!empty($attachments)){
                foreach($attachments as $path=>$name){
                    if(isset($bcc[$email]) && $bcc[$email] ?? null !== null) throw new Exception(message: $lang['errMailFormat']);
                    $mail->addAttachment(path: $path,name: $name);
                }
            }
            $mail->isHTML(isHtml: $isHTML);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;
            $mail->send();
        }catch(Exception $e){
            $failedSend = false;
        }
        $db->close();
        return $failedSend;
    }
    /**
     * Generates an API key
     * @return string api key generated
     */
    public function generateAPI(): string{
        $db = new Database(file: 'NetworksCMS',flags: Database::READ_ONLY);
        $results = $db->selectTable(name: 'api')->select(selector: 'key',mode: Database::BOTH);
        $api = implode(separator: '-', array: str_split(string: substr(string: strtolower(string: md5(string: microtime().rand(min: 1000, max: 9999))), offset: 0, length: 44), length: 8));
        if($results){
            foreach($results as $row){
                if(isset($row['row'])){
                    while($api===$row['row']){
                        $api = implode(separator: '-', array: str_split(string: substr(string: strtolower(string: md5(string: microtime().rand(min: 1000, max: 9999))), offset: 0, length: 44), length: 8));
                    }
                }
            }
        }
        return $api;
    }
}
?>
<?php
namespace networks\libs;

use SSQL;

/**
 * Utils
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Utils{
    public function __construct() {
        
    }
    /**
     * Converts array to named parameters
     *
     * @param array $arr Array of strings to convert into named prameters
     * @return array
     */
    public function extractParam(array $arr){
        $args = array();
        foreach($arr as $a){
            $e = explode(':',$a);
            $args[$e[0]] = $e[1];
        }
        return $args;
    }
    /**
     * Gets the file size in human-readable format
     * 
	 * @param string $file Filepath
	 * @param int $digits Digits to display
	 * @return string|bool Size (KB, MB, GB, TB) or boolean
	 */	
	public function getFilesize(string $file,int $digits = 2):string|bool{
		$bytes = filesize($file);
		if ($bytes < 1024) return $bytes.' B';
		elseif ($bytes < 1048576) return round($bytes / 1024, $digits).' KB';
		elseif ($bytes < 1073741824) return round($bytes / 1048576, $digits).' MB';
		elseif ($bytes < 1099511627776) return round($bytes / 1073741824, $digits).' GB';
		else return round($bytes / 1099511627776, $digits).' TB';	
	}
    /**
     * Converts filesize to human-readable format
     *
     * @param int|float $size Filesize
     * @return string human-readable size
     */
	protected function sizeConversion(int|float $size):string{
        $unit = array('B','KB','MB','GB','TB','PB');
        return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit[$i];
	}
		
	/**
	 * Method that controls the memory used
	 * @param string $type Memory control type (available, peak or current usage)
	 * @return string
	 */
	public function getMemory($type = 'usage') {
        if ((string) $type === 'available') {
            $memoryAvailable = filter_var(ini_get("memory_limit"), FILTER_SANITIZE_NUMBER_INT);
            $memoryAvailable = $memoryAvailable * 1024 * 1024;
            $size = (int) $memoryAvailable;
        } elseif ((string) $type === 'peak') {
            $size = (int) memory_get_peak_usage(true);
        } elseif ((string) $type === 'usage') {
            $size = (int) memory_get_usage(true);
        } else {
            $size = 0;
        }
        return self::sizeConversion($size);
	}
    /**
     * Checks for loaded modules
     *
     * @param array $modules PHP Modules
     * @return string
     */
    public function checkModules(array $modules = array('mbstring', 'json', 'gd', 'dom')):string{
        $bad='';
        $missing='';
        foreach ($modules as $module) {
			if (!extension_loaded($module)) {
				$errorText = 'PHP module <b>'.$module.'</b> is not installed.';
				error_log('[ERROR] '.$errorText, 0);
		
				$bad = true;
				$missing .= $errorText.PHP_EOL;
			}
		}
		if ($bad) {
			return 'PHP modules missing:';
			return $missing;
			exit();
		}
    }
    /**
     * Checks if mail function exists
     *
     * @return bool
     */
    public function checkMail():bool{
        if(function_exists('mail')) return true;
        else return false;
    }
    /**
     * Send mail to user
     *
     * @param string $name Senders name
     * @param string $from Senders email
     * @param array{name: string, email: string}|string $to Targets name and email
     * @param string $subject Emails subject
     * @param string $body Emails body
     * @param string $contentType Emails content **HTML** or **TEXT**
     * @param string $cc Carbon-Copy email address
     * @param string $bcc Bind Carbon-Copy email address
     * @return boolean
     */
    public function sendMail(string $name, string $from, array|string $to, string $subject, string $body, string $contentType='html', array|string $cc='', array|string $bcc=''):bool{
        $sql = new SSQL();
        $charset = NW_CHARSET;
        if(file_exists(NW_SQL_CREDENTIALS)){
            $c = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            if($sql->setCredential($c['server'],$c['user'],$c['psw'])){
                $db = $sql->selectDB($c['db']);
                $charset = $db->selectData('config',['charset'])[0]['charset'];
            }
        }
        if(is_array($to))
            $to = implode(',',array_map(function($name, $email) {
                return "$name <$email>";
            }, array_keys($to), $to));
        if(is_array($cc))
            $cc = implode(',',array_map(function($name, $email) {
                return "$name <$email>";
            }, array_keys($cc), $cc));
        if(is_array($bcc))
            $bcc = implode(',',array_map(function($name, $email) {
                return "$name <$email>";
            }, array_keys($bcc), $bcc));
        
        $headers = "From: ".$name." <".$from.">\r\n";
        $headers.= "Reply-To: ".$from."\r\n";
        $headers.= "MIME-Version: 1.0\r\n";
        if(strtolower($contentType) == 'html')
			$headers .= 'Content-type: text/html; charset="' .$charset. '"'."\r\n";
		else
			$headers .= 'Content-type: text/plain; charset="' .$charset. '"'."\r\n";
        $headers .= 'Content-transfer-encoding: 8bit'."\r\n";
        $headers .= 'Date: '.date("D, j M Y G:i:s O")."\r\n"; // Sat, 7 Jun 2001 12:35:58 -0700
        if($cc != "")
			$headers .= 'Cc: '.$cc."\r\n";
		if($bcc != "")
			$headers .= 'Bcc: '.$bcc."\r\n";
        return mail($to, $subject, $body, $headers);
    }
}
?>
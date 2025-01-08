<?php
    namespace networks\libs;
    include_once dirname(__DIR__).'/init.php';
    include_once dirname(__DIR__).'/libs/ssql.lib.php';
    use SSQL;
    class Lang{
        protected array $langObj;
        private string $lang, $country;
        
        /**
         * Receives language folder based on language and country
         *
         * @param string $lang Language to use, **en**
         * @param string $country Country to the language, **us**
         * @param bool $auto Automatically get the language from the configuration
         */
        public function __construct(string $lang='en', string $country='us',bool $auto=true){
            if(file_exists(dirname(__DIR__).'/assets/sql/credentials.json')&&$auto){
                $cred = json_decode(file_get_contents(dirname(__DIR__).'/assets/sql/credentials.json'),true);
                $sql = new SSQL();
                if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                    $db = $sql->selectDB($cred['db']);
                    $getLang = explode('-',$db->selectData('config',['lang'])[0]['lang']);
                    $this->lang = $getLang[0];
                    $this->country = $getLang[1];
                    $sql->close();
                }
            }

            $this->langObj = json_decode(file_get_contents(dirname(__DIR__)."/languages/" . strtolower("{$this->lang}-{$this->country}") . ".json"), true);
            foreach(array_diff(scandir(dirname(__DIR__).'/plugins'),['.','..']) as $plugin){
                if(file_exists(dirname(__DIR__)."/plugins/{$plugin}/lang/" . strtolower("{$this->lang}-{$this->country}") . ".json")){
                    $this->langObj = array_merge($this->langObj, json_decode(file_get_contents(dirname(__DIR__)."/plugins/{$plugin}/lang/" . strtolower("{$this->lang}-{$this->country}") . ".json"), true));
                }
            }
        }
        /**
         * Receive the value of the language.
         *
         * @param string|array ...$lookup Subject to look for in array
         * @return mixed Returns the value of the type, otherwise False.
         */
        public function get(string|array ...$lookup) : mixed{
            if(is_array($lookup[0])) $lookup = array_merge(...$lookup);
            $lastLook = null;
            foreach($lookup as $look){
                if(isset($this->langObj[$look])||isset($lastLook[$look])) $lastLook = !$lastLook ? $this->langObj[$look] : $lastLook[$look];
                else return false;
            }
            return $lastLook;
        }
        /**
         * Get current language
         * @return string language-country
         */
        public function current(): string{
            return "{$this->lang}-{$this->country}";
        }
    }
?>
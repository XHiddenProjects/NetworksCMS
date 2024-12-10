<?php
    namespace networks\libs;
    require_once(dirname(__DIR__).'/init.php');
    class Lang{
        protected Array $langObj;
        /**
         * Recieves language folder based on language and country
         *
         * @param String $lang Language to use, **en**
         * @param String $country Country to the language, **us**
         */
        public function __construct(String $lang='en', String $country='us') {
            $this->langObj = json_decode(file_get_contents(dirname(__DIR__).'/languages/'.strtolower($lang.'-'.$country).'.json'),true);
        }
        /**
         * Recieve the value of the language.
         *
         * @param String $type Type of language
         * @param String $lookup Subject to the type
         * @return String|False Returns the value of the type, otherwise False.
         */
        public function get(String $type, string $lookup) : String|False{
            if(isset($this->langObj[$type])&&isset($this->langObj[$type][$lookup]))
                return $this->langObj[$type][$lookup];
            else return false;
        }
    }
?>
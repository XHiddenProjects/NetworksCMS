<?php
use networks\libs\Dictionary;
use networks\libs\Templates;
require_once(dirname(__DIR__).'/init.php');
require_once(dirname(__DIR__).'/libs/templates.lib.php');
require_once(dirname(__DIR__).'/libs/dictionary.lib.php');

if(!file_exists(NW_SQL_CREDENTIALS))
    echo '<script>window.open("./install","_self")</script>';
else
    echo (new Templates('error'))->load((new Dictionary())->merge(NW_DICTIONARY_CONFIG,NW_DICTIONARY_PAGES,NW_DICTIONARY_LANG,NW_DICTIONARY_DEFAULT,NW_DICTIONARY_META,NW_DICTIONARY_HOOKS));
?>
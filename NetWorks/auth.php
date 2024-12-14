<?php
use networks\libs\Dictionary;
use networks\libs\Templates;
require_once('init.php');

if(!file_exists(NW_SQL_CREDENTIALS))
    echo '<script>window.open("./install","_self")</script>';
else
    echo (new Templates('auth'))->load((new Dictionary())->merge(NW_DICTIONARY_CONDITIONS,NW_DICTIONARY_CONFIG,NW_DICTIONARY_PAGES,NW_DICTIONARY_LANG,NW_DICTIONARY_DEFAULT,NW_DICTIONARY_META,NW_DICTIONARY_HOOKS));
?>
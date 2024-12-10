<?php

use networks\libs\Templates;
use networks\libs\Dictionary;

require_once('init.php');
require_once('libs/templates.lib.php');
require_once('libs/dictionary.lib.php');

if(!file_exists(NW_SQL_CREDENTIALS)){
    echo (new Templates('install'))->load((new Dictionary())->merge(NW_DICTIONARY_CONFIG,NW_DICTIONARY_LANG,NW_DICTIONARY_DEFAULT,NW_DICTIONARY_META,NW_DICTIONARY_HOOKS));
}else
    echo '<script>window.history.back();</script>';
?>
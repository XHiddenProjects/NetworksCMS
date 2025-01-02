<?php
/**
 * Autoloads all the libraries
 */
class Autoloader{
    public function __construct() {
        foreach(array_diff(scandir(dirname(__FILE__).'/libs'),['.','..']) as $lib)
            include_once(dirname(__FILE__).'/libs/'.$lib);
        foreach(array_diff(scandir(dirname(__FILE__).'/Exceptions'),['.','..']) as $exception)
            include_once(dirname(__FILE__).'/Exceptions/'.$exception);
    }
}
(new Autoloader());
?>
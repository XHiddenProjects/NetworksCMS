<?php
namespace networks\Exception;

use Exception;

class FileHandlingException extends Exception{
    protected String $fPath;
    public function __construct(String $message, String $path='', int $code=0) {
        parent::__construct($message,$code);
        $this->fPath = $path;
    }
    /**
     * Returns the pathname
     *
     * @return String
     */
    public function getPath() : String {
        return $this->fPath;
    }
}
?>
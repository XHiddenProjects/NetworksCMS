<?php
namespace networks\Exception;

use Exception;

class PluginHandlingException extends Exception{
    protected String $status;
    public function __construct(String $message, String $status, int $code=0) {
        parent::__construct($message,$code);
        $this->status = $status;
    }
    /**
     * Returns the plugin status
     *
     * @return string
     */
    public function getPluginStats():string{
        return $this->status;
    }
}
?>
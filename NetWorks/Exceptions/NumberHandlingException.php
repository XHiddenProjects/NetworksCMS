<?php
namespace networks\Exception;
use Exception;
class NumberHandlingException extends Exception{
    public function __construct(string $message, int $code=0) {
        parent::__construct($message,$code);
    }
}
?>
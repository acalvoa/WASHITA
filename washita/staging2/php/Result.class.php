<?php

class Result{
    /**
     * @var boolean
     */
    var $success;
    /**
     * @var string
     */
    var $message;
    var $value;
    
    public function __construct(){
       $this->message= "";
    }

    /**
     * @param string $message
     * @return Result
     */
    public static function Fail($message){
        $result = new Result();
        $result->success = false;
        $result->message.= $message;
        return $result;
    }

    public static function Success(){
        $result = new Result();
        $result->success = true;
        return $result;
    }

    public function __toString() {
        return "Result";
    }
}

?>
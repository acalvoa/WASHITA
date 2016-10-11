<?php
require_once(dirname(__FILE__)."/../_config.php");

class CurrentDateTime{
    private static $currentDateTime;
    
    private function __construct(){
        
    }
    
    public static function SetCurrentDateTime(DateTime $dateTime){
        self::$currentDateTime = $dateTime;
    }
    
    public static function Now(){
        if(isset(self::$currentDateTime)){
            return self::$currentDateTime;
        }
        
        $datetime = new DateTime();

        return $datetime;
    }
}

?>
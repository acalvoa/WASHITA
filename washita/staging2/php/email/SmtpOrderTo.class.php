<?php 
include_once(dirname(__FILE__)."/../../_config.php");

class SmtpOrderTo{

    /** @var string */
    var $To;
    
    /** @var string */
    var $Bcc;

    public static function GetByCityId($cityId){

        global $AdminOrdersCityIdVina, $AdminOrdersCityIdSantiago;
        global $smtpOrderVina, $smtpOrderBccVina, $smtpOrderSantiago, $smtpOrderBccSantiago;
        $result = new SmtpOrderTo();

        if($cityId == $AdminOrdersCityIdVina){
            $result->To = $smtpOrderVina;
            $result->Bcc = $smtpOrderBccVina;
        }
        else if($cityId == $AdminOrdersCityIdSantiago){
            $result->To = $smtpOrderSantiago;
            $result->Bcc = $smtpOrderBccSantiago;
        }

        return $result;
        
    } 
}
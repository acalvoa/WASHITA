<?php

// task should be in the form 
//  /staging/admin/tasks/task_feedback_email.php?secret=super; 

if($_GET['secret'] !='super'){
    exit;
}

include_once(dirname(__FILE__)."/../../_config.php");
include_once(dirname(__FILE__)."/../../php/_helpers.php");
include_once(dirname(__FILE__)."/../../php/CurrentDateTime.class.php");
include_once(dirname(__FILE__)."/../../php/MailService.class.php");
include_once(dirname(__FILE__)."/../../php/OrderRequiredFeedback.class.php");
include_once(dirname(__FILE__)."/../../php/Order.class.php");



$mailService = new MailService();
$orders = getOrdersRequiredFeedbackNotification();
foreach ($orders as $key => $orderFeedback){
    if(!$orderFeedback->FeedbackCode){
        $orderFeedback->FeedbackCode = setTemporaryCodeForOrder($orderFeedback->OrderNumber);
    }  
    try{
        if($orderFeedback->FeedbackCode){
             $mailService->SendFeedbackRequest($orderFeedback);
             // PHP mail has akward bug 
             // Email can be sent, but the result will be False
             // As a workaround, though not ideal, set that email is sent
             Order::SetFeedbackIsRequested($orderFeedback->OrderNumber);   
        }
    }
    catch(Exception $e) {
        continue;   
    }
}


// returns order numbers required feedback
function getOrdersRequiredFeedbackNotification(){
    global $DBName,
           $OrderSendFeedbackAfterMinutes, 
           $OrderSendFeedbackSince;
    
    $currentDatetime = CreateDateTimeImmutableFromMutable(CurrentDateTime::Now());
    $maxDateTime = $currentDatetime->modify("+".$OrderSendFeedbackAfterMinutes." minutes");
    
    $query = "SELECT ORDER_NUMBER, FEEDBACK_CODE, EMAIL, NAME, CITY_AREA_ID FROM `".$DBName."`.`orders`  
              WHERE IS_FEEDBACK_REQUESTED <> 1 AND 
                    DROPOFF_TILL >= '".$OrderSendFeedbackSince."' AND 
                    DROPOFF_TILL < '".$maxDateTime->format("Y-m-d H:i")."'
                    AND PAYMENT_STATUS = 2";
    $array=array();
    try
    {
        $mysqli = OpenMysqlConnection(); 
        $sql_result = $mysqli->query($query);
        if($sql_result){
            while ($row = $sql_result->fetch_assoc()) {
                $orderFeedback = new OrderRequiredFeedback();
                $orderFeedback->OrderNumber = $row["ORDER_NUMBER"];
                $orderFeedback->FeedbackCode = $row["FEEDBACK_CODE"];
                $orderFeedback->Email = $row["EMAIL"];
                $orderFeedback->Name = $row["NAME"];
                $orderFeedback->CityAreaId = $row["CITY_AREA_ID"];

                

                $array[] = $orderFeedback;
            }
            $sql_result->free();
        }
        $mysqli->close();
    }
    catch(Exception $e) {
            
    }
    return $array;
}

function setTemporaryCodeForOrder($orderNumber){
    global $DBName;
    //Generates random code, tries maximum 10 times
    $result = '';
    for ($x = 0; $x <= 10; $x++) {
        try{
            $code = generateRandomCode();
            
            $mysqli = OpenMysqlConnection(); 
            // Write to the database requested data
            $query = "UPDATE `".$DBName."`.`orders`
                    SET FEEDBACK_CODE='".$code."'
                    WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'";
            if($mysqli->query($query)){
                $mysqli->close();
                $result = $code;
                //success, stop iteration
                break; 
            }   
        }
        catch(Exception $e) {
            
        }
    } 
    return $result;
      
}




function generateRandomCode(){
        return   md5(str_shuffle( "0123456789abcdefghijklmnoABCDEFGHIJ" ) );
}


echo 'End of file';

?>

<?php

// Successful payment
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/flowAPI.php");
require_once(dirname(__FILE__)."/../MailService.class.php");
require_once(dirname(__FILE__)."/../PickupTime.class.php");
require_once(dirname(__FILE__)."/../Order.class.php");
require_once(dirname(__FILE__)."/../DiscountCoupon.class.php");


// Inicializa la clase de flowAPI
$flowAPI = new flowAPI();

try {
	// Lee los datos enviados por Flow
	$flowAPI->read_result();
} catch (Exception $e) {
	error_log($e->getMessage());
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Ha ocurrido un error interno', true, 500);
	return;
}

$FLOW_ORDER_NUMBER = $flowAPI->getOrderNumber(); // Order number // N° Orden del Comercio
$FLOW_AMOUNT = $flowAPI->getAmount(); // Transaction amount// Monto de la transacción
$FLOW_NUMBER = $flowAPI->getFlowNumber(); // If $FLOW_STATUS = "EXITO" (success) No. Order Flow /// Si $FLOW_STATUS = "EXITO" el N° de Orden de Flow
$FLOW_PAYER_EMAIL = $flowAPI->getPayer(); // The payer email // El email del pagador
$FLOW_DESCRIPTION = $flowAPI->getConcept(); 

$mysqli = OpenMysqlConnection(); 
// Insert the record about Flow responce for the history,
// even if Order record does not exists 
$insertFlowRecord = "INSERT INTO `".$GLOBALS["DBName"]."`.`flow_payment`";
$insertFlowRecord.= "(`ORDER_NUMBER`,`FLOW_NUMBER`,`TRANSACTION_AMOUNT`,`PAYER_EMAIL`,`DESCRIPTION`,`RESPONSE_TYPE`)";
$insertFlowRecord.= "VALUES('".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."', '".$mysqli->real_escape_string($FLOW_NUMBER)."', '".$mysqli->real_escape_string($FLOW_AMOUNT)."', '".$mysqli->real_escape_string($FLOW_PAYER_EMAIL)."', '".$mysqli->real_escape_string($FLOW_DESCRIPTION)."','".FlowResponseType::SuccessedPayment."')";
$mysqli->query($insertFlowRecord);    

$errorMsg = "";
$pickupTimeText = "Error";

$order = Order::GetOrderByNumber($FLOW_ORDER_NUMBER);
if ($order != null) {
    if(($FLOW_AMOUNT != $order->ActualPriceWithDiscount)
        // && 
        // ($FLOW_AMOUNT != $order->AdditionalPriceWithDiscount)
       ){
        $errorMsg .= "Order price (main or additional) and Flow paid price '".$FLOW_AMOUNT."'are different.";      
    }
    
    if(isset($order->PaymentStatus) && $order->PaymentStatus == OrderPaymentStatus::Paid){
        $errorMsg .= "Order is already paid. Please check for double payment!";              
    }
    
    $pickupTimeText = $order->PickupTime->asText();
}
else{
    $errorMsg .= "Cannot find order in the system by order number: '".$FLOW_ORDER_NUMBER."'."; 
}


if(!empty($errorMsg)){
    $errorMsg.="Flow information:\n\r";
    $errorMsg.="Order number: ".$FLOW_ORDER_NUMBER."\n\r";
    $errorMsg.="Amount: ".$FLOW_AMOUNT."\n\r";
    $errorMsg.="Flow number: ".$FLOW_NUMBER."\n\r";
    $errorMsg.="Payer email: ".$FLOW_PAYER_EMAIL."\n\r";
    $errorMsg.="Description: ".$FLOW_DESCRIPTION."\n\r";
    
    $mailService = new MailService();
    $mailService->NotifyAdmin("Error in the system! 'Flow' returned successful status of confirmed payment. Order: ".$FLOW_ORDER_NUMBER, $errorMsg);
    
    RedirectToErrorPage($order_number, "We are sorry. The order is paid, but there is an error in the system. We will contact you soon.\n\r".$errorMsg);
    exit();
}
else{ // if all succesfull set order as 'Paid'
    Order::SetOrderPaymentStatus($FLOW_ORDER_NUMBER, OrderPaymentStatus::Paid);
    $order->UseCoupon();

    RedirectToSuccessPaymentPage($FLOW_ORDER_NUMBER,$pickupTimeText);
    //RedirectToMessagePage("Payment","<p>Thank you for the payment. Order is paid.</p>");

    // $isAdditionalPayment = $order->AdditionalPriceWithDiscount > 0;
    // if($isAdditionalPayment){
    //     RedirectToMessagePage("Payment","<p>Thank you for the payment. Order is paid.</p>");
    // }
    // else{ // usual payment
    //     // SEND EMAIL
    //     $mailService = new MailService();
    //     $mailService->SendNotification($FLOW_ORDER_NUMBER);
    //     // END OF EMAIL SENDING
        
    //     // Redirect to success page
    //     RedirectToSuccessPage($FLOW_ORDER_NUMBER,$pickupTimeText);
    //     exit();
    // }
}

?>

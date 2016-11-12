<?php
// Failed payment
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/flowAPI.php");

LogHttpHeaders();

// Inicializa la clase de flowAPI
$flowAPI = new flowAPI();

try {
	// Lee los datos enviados por Flow
	//$flowAPI->read_result();
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


if(!empty($FLOW_ORDER_NUMBER) ||
   !empty($FLOW_NUMBER) ||
   !empty($FLOW_PAYER_EMAIL))
{ 
    $mysqli = OpenMysqlConnection(); 
    // Insert the record about Flow responce for the history,
    // even if Order record does not exists 
    $insertFlowRecord = "INSERT INTO `".$GLOBALS["DBName"]."`.`flow_payment`";
    $insertFlowRecord.= "(`ORDER_NUMBER`,`FLOW_NUMBER`,`TRANSACTION_AMOUNT`,`PAYER_EMAIL`,`DESCRIPTION`,`RESPONSE_TYPE`)";
    $insertFlowRecord.= "VALUES('".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."', '".$mysqli->real_escape_string($FLOW_NUMBER)."', '".$mysqli->real_escape_string($FLOW_AMOUNT)."', '".$mysqli->real_escape_string($FLOW_PAYER_EMAIL)."', '".$mysqli->real_escape_string($FLOW_DESCRIPTION)."','".FlowResponseType::FailedPayment."')";
    $mysqli->query($insertFlowRecord);  


    // set failed payment
    $updateOrderQuery = "UPDATE `".$GLOBALS["DBName"]."`.`orders` SET PAYMENT_STATUS='".OrderPaymentStatus::FailedOrPartialPayment."' WHERE `ORDER_NUMBER`='".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."' AND `PAYMENT_STATUS`<>'".OrderPaymentStatus::Paid."'";        
    $mysqli->query($updateOrderQuery);

    //close connection
    $mysqli->close();
}

RedirectToErrorPage($FLOW_ORDER_NUMBER, "El pago no pudo ser procesado.");

?>
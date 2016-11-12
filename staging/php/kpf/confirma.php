<?php
//Confirms or rejects transaction from Flow service 
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/flowAPI.php");

LogHttpHeaders();

// Inicializa la clase de flowAPI
$flowAPI = new flowAPI();

try {
	// Lee los datos enviados por Flow
	$flowAPI ->read_confirm();
	
} catch (Exception $e) {
    error_log($e->getMessage());
    // Si hay un error responde false
	echo $flowAPI->build_response(false);
	return;
}

//Recupera Los valores de la Orden
$FLOW_STATUS = $flowAPI->getStatus();  // The result of the transaction (success or failure)// El resultado de la transacción (EXITO o FRACASO)
$FLOW_ORDER_NUMBER = $flowAPI->getOrderNumber(); // Order number // N° Orden del Comercio
$FLOW_AMOUNT = $flowAPI->getAmount(); // Transaction amount// Monto de la transacción
$FLOW_NUMBER = $flowAPI->getFlowNumber(); // If $FLOW_STATUS = "EXITO" (success) No. Order Flow /// Si $FLOW_STATUS = "EXITO" el N° de Orden de Flow
$FLOW_PAYER_EMAIL = $flowAPI->getPayer(); // The payer email // El email del pagador

$confirmTransaction = ($FLOW_STATUS == "EXITO");//Success


 try {
    $mysqli = OpenMysqlConnection(); 
    // Insert the record about Flow responce for the history,
    // even if Order record does not exists 
    $insertFlowRecord = "INSERT INTO `".$GLOBALS["DBName"]."`.`flow_payment`";
    $insertFlowRecord.= "(`ORDER_NUMBER`,`FLOW_NUMBER`,`STATUS`,`TRANSACTION_AMOUNT`,`PAYER_EMAIL`,`RESPONSE_TYPE`) ";
    $insertFlowRecord.= "VALUES('".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."', '".$mysqli->real_escape_string($FLOW_NUMBER)."', '".$mysqli->real_escape_string($FLOW_STATUS)."', '".$mysqli->real_escape_string($FLOW_AMOUNT)."', '".$mysqli->real_escape_string($FLOW_PAYER_EMAIL)."', '".FlowResponseType::TransactionConfirmationRequest."')";
    $confirmTransaction &= ($mysqli->query($insertFlowRecord) === TRUE);    

    if($confirmTransaction){
        //Check that order exists and it is not paid
        $orderQuery = "SELECT * FROM `".$GLOBALS["DBName"]."`.`orders` WHERE `ORDER_NUMBER`='".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."' LIMIT 1";        
        $orderResult = $mysqli->query($orderQuery);
        if ($orderResult->num_rows > 0) {
            $orderRow = $orderResult->fetch_assoc();
            
            $orderPaymentStatus = $orderRow["PAYMENT_STATUS"];
            // Check that order is valid for payment
                                
            $confirmTransaction = (isset($orderPaymentStatus) && $orderPaymentStatus != OrderPaymentStatus::Paid 
                                    &&
                                    $FLOW_AMOUNT > 0
                                    &&
                                    ( ($FLOW_AMOUNT == $orderRow["ACTUAL_PRICE_WITH_DISCOUNT"])
                                    //     ||
                                    //   ($FLOW_AMOUNT == $orderRow["ADDITIONAL_PRICE_WITH_DISCOUNT"])
                                    )
                                  );
                                
        
                                
            //close connection
            $orderResult->free();
        }
        else{
            $confirmTransaction = false;
        }

    }

    if(!$confirmTransaction){
        // set failed payment   
        $updateOrderQuery = "UPDATE `".$GLOBALS["DBName"]."`.`orders` SET PAYMENT_STATUS='".OrderPaymentStatus::FailedOrPartialPayment."' WHERE `ORDER_NUMBER`='".$mysqli->real_escape_string($FLOW_ORDER_NUMBER)."' AND `PAYMENT_STATUS`<>'".OrderPaymentStatus::Paid."'";        
        $mysqli->query($updateOrderQuery);
    }
    $mysqli->close();

    } 
    catch (Exception $e) {
       // RedirectToErrorPage($orderNumber,"Internal error Mail_sending");
}
/*Aquí puede validar la Order
 * Si acepta la Orden responder $flowAPI ->build_response(true)
 * Si rechaza la Orden responder $flowAPI ->build_response(false)
 */

if($confirmTransaction) {
	// La transacción fue aceptada por Flow
	// Aquí puede actualizar su información con los datos recibidos por Flow
	echo $flowAPI->build_response(true); // Confirm transaction // Comercio acepta la transacción
} else {
	echo $flowAPI->build_response(false); // Reject transaction // Comercio rechaza la transacción
}

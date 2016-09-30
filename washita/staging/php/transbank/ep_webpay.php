<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This sctipr is used like a endpoint of transbank process
*/
require_once(dirname(__FILE__)."/Webpay.php");

$action = $_GET['action'];
if(!empty($action) AND !is_null($action)){
	if($action == "BEGIN_TRANS"){
		$webpay = new Webpay();
		$webpay->START_TRANS();
	}
	else if($action == "VERIFY_TRANS"){
		
	}
	else{
		
	}
}
?>
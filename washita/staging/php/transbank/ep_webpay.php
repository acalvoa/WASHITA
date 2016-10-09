<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This sctipr is used like a endpoint of transbank process
*/
require_once(dirname(__FILE__)."/Webpay.php");
require_once(dirname(__FILE__)."/OneClick.php");

$action = $_GET['action'];
if(!empty($action) AND !is_null($action)){
	if($action == "BEGIN_TRANS"){
		$webpay = new Webpay();
		$webpay->START_TRANS();
	}
	else if($action == "VERIFY_TRANS"){
		$webpay = new Webpay();
		$webpay->VERIFY();
	}
	else if($action == "ONECLICK_INSCRIPTION"){
		$oneclick = new OneClick();
		$url = $oneclick->INIT_INSCRIPTION();
		$retorno["status"] = 1;
		$retorno["url"] = $url;
		echo json_encode($retorno);
	}
}
?>
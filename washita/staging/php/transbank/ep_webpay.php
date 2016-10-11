<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This sctipr is used like a endpoint of transbank process
*/

$action = $_GET['action'];
if(!empty($action) AND !is_null($action)){
	if($action == "BEGIN_TRANS"){
		require_once(dirname(__FILE__)."/Webpay.php");
		$webpay = new Webpay();
		$webpay->START_TRANS();
	}
	else if($action == "BEGIN_TRANS_WS"){
		require_once(dirname(__FILE__)."/Webpay.php");
		$webpay = new Webpay();
		$webpay->START_TRANS_WS();
	}
	else if($action == "VERIFY_TRANS"){
		require_once(dirname(__FILE__)."/Webpay.php");
		$webpay = new Webpay();
		$webpay->VERIFY();
	}
	else if($action == "VERIFY_TRANS_WS"){
		require_once(dirname(__FILE__)."/Webpay.php");
		$webpay = new Webpay();
		$webpay->RESULT_WEBPAY_WS();
	}
	else if($action == "ONECLICK_INSCRIPTION"){
		require_once(dirname(__FILE__)."/OneClick.php");
		if(isset($_GET['ws']) && $_GET['ws'] == "true"){
			$oneclick = new OneClick();
			$oneclick->INIT_INSCRIPTION(true);
		}
		else
		{
			$oneclick = new OneClick();
			$oneclick->INIT_INSCRIPTION();
		}
	}
	else if($action == "FINISH_ONECLICK_INSCRIPTION"){
		require_once(dirname(__FILE__)."/OneClick.php");
		if(isset($_GET['ws']) && $_GET['ws'] == "true"){
			$oneclick = new OneClick();
			$oneclick->FINISH_INSCRIPTION($_POST['TBK_TOKEN'], true);
		}
		else
		{
			$oneclick = new OneClick();
			$oneclick->FINISH_INSCRIPTION($_POST['TBK_TOKEN']);
		}
	}
	else if($action == "AUTHORIZE_ONECLICK"){
		require_once(dirname(__FILE__)."/OneClick.php");
		if(isset($_GET['ws']) && $_GET['ws'] == "true"){
			$oneclick = new OneClick();
			$oneclick->AUTHORIZE($_POST['TBK_USER'],true);
		}
		else
		{
			$oneclick = new OneClick();
			$oneclick->AUTHORIZE($_POST['TBK_USER']);
		}
	}
}
?>
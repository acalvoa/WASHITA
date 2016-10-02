<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for use the OneCLick SOAP Call API provided by transbank.
*/
require_once("OneClickSoap.php");
require_once(dirname(__FILE__).'/vendor/soap-wsse.php');
require_once(dirname(__FILE__).'/vendor/soap-validation.php');

/* CLASES AUTOGENERATED BY SOAP WSDL SERVICE*/

class removeUser{
	var $arg0;//oneClickRemoveUserInput
}
class oneClickRemoveUserInput{
	var $tbkUser;//string
	var $username;//string
}
class baseBean{
}
class removeUserResponse{
	var $return;//boolean
}
class initInscription{
	var $arg0;//oneClickInscriptionInput
}
class oneClickInscriptionInput{
	var $email;//string
	var $responseURL;//string
	var $username;//string
}
class initInscriptionResponse{
	var $return;//oneClickInscriptionOutput
}
class oneClickInscriptionOutput{
	var $token;//string
	var $urlWebpay;//string
}
class finishInscription{
	var $arg0;//oneClickFinishInscriptionInput
}
class oneClickFinishInscriptionInput{
	var $token;//string
}
class finishInscriptionResponse{
	var $return;//oneClickFinishInscriptionOutput
}
class oneClickFinishInscriptionOutput{
	var $authCode;//string
	var $creditCardType;//creditCardType
	var $last4CardDigits;//string
	var $responseCode;//int
	var $tbkUser;//string
}
class codeReverseOneClick{
	var $arg0;//oneClickReverseInput
}
class oneClickReverseInput{
	var $buyorder;//long
}
class codeReverseOneClickResponse{
	var $return;//oneClickReverseOutput
}
class oneClickReverseOutput{
	var $reverseCode;//long
	var $reversed;//boolean
}
class authorize{
	var $arg0;//oneClickPayInput
}
class oneClickPayInput{
	var $amount;//decimal
	var $buyOrder;//long
	var $tbkUser;//string
	var $username;//string
}
class authorizeResponse{
	var $return;//oneClickPayOutput
}
class oneClickPayOutput{
	var $authorizationCode;//string
	var $creditCardType;//creditCardType
	var $last4CardDigits;//string
	var $responseCode;//int
	var $transactionId;//long
}
class reverse{
	var $arg0;//oneClickReverseInput
}
class reverseResponse{
	var $return;//boolean
}
class OneClickWs 
{
 	var $soapClient;
 
	private static $classmap = array('removeUser'=>'removeUser'
	,'oneClickRemoveUserInput'=>'oneClickRemoveUserInput'
	,'baseBean'=>'baseBean'
	,'removeUserResponse'=>'removeUserResponse'
	,'initInscription'=>'initInscription'
	,'oneClickInscriptionInput'=>'oneClickInscriptionInput'
	,'initInscriptionResponse'=>'initInscriptionResponse'
	,'oneClickInscriptionOutput'=>'oneClickInscriptionOutput'
	,'finishInscription'=>'finishInscription'
	,'oneClickFinishInscriptionInput'=>'oneClickFinishInscriptionInput'
	,'finishInscriptionResponse'=>'finishInscriptionResponse'
	,'oneClickFinishInscriptionOutput'=>'oneClickFinishInscriptionOutput'
	,'codeReverseOneClick'=>'codeReverseOneClick'
	,'oneClickReverseInput'=>'oneClickReverseInput'
	,'codeReverseOneClickResponse'=>'codeReverseOneClickResponse'
	,'oneClickReverseOutput'=>'oneClickReverseOutput'
	,'authorize'=>'authorize'
	,'oneClickPayInput'=>'oneClickPayInput'
	,'authorizeResponse'=>'authorizeResponse'
	,'oneClickPayOutput'=>'oneClickPayOutput'
	,'reverse'=>'reverse'
	,'reverseResponse'=>'reverseResponse'

	);

	function __construct($url='https://webpay3g.orangepeople.cl/webpayserver/wswebpay/OneClickPaymentService?wsdl')
	{
		$this->soapClient = new OneClickSoap($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
	}
 
	function removeUser($removeUser)
	{
		$removeUserResponse = $this->soapClient->removeUser($removeUser);
		return $removeUserResponse;
	}
	function initInscription($initInscription)
	{
		$initInscriptionResponse = $this->soapClient->initInscription($initInscription);
		return $initInscriptionResponse;
	}
	function finishInscription($finishInscription)
	{
		$finishInscriptionResponse = $this->soapClient->finishInscription($finishInscription);
		return $finishInscriptionResponse;
	}
	function authorize($authorize)
	{
		$authorizeResponse = $this->soapClient->authorize($authorize);
		return $authorizeResponse;
	}
	function codeReverseOneClick($codeReverseOneClick)
	{
		$codeReverseOneClickResponse = $this->soapClient->codeReverseOneClick($codeReverseOneClick);
		return $codeReverseOneClickResponse;
	}
	function reverse($reverse)
	{
		$reverseResponse = $this->soapClient->reverse($reverse);
		return $reverseResponse;
	}
}
?>
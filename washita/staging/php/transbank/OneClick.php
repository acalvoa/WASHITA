<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for use the OneCLick SOAP Call API provided by transbank.
*/
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../hybridauth/WashitaUser.php");
require_once(dirname(__FILE__)."/OrdersGenerator.php");
require_once("MySQLDB.php");
require_once("OneClickWs.php");

/**
*	This class is the main class of the OneClick Process.
*/
public class OneClick extends MySQLDB{
	private $TBK_SERVER_CERT;
	private $TBK_PRIVATE_KEY;
	private $TBK_CERT_FILE;
	private $WASHITA_USERNAME;
	private $WASHITA_EMAIL;
	private $ONECLICK_URL_INSCRIPTION;
	function __construct(){
		$this->SERVER_CERT = $GLOBAL['TBK_CERT_FILE_WS'];
		$this->PRIVATE_KEY = $GLOBAL['TBK_PRIVATE_KEY_WS'];
		$this->CERT_FILE = $GLOBAL['TBK_SERVER_CERT_FILE'];
		$this->ONECLICK_URL_INSCRIPTION = $GLOBAL['ONECLICK_URL_INSCRIPTION'];
		$this->CHECKCONFIG();
	}
	function INIT_INSCRIPTION(){
		$this->GETUSERPARAM();
		$oneClickService = new OneClickWS();
		$oneClickInscriptionInput = new oneClickInscriptionInput();
		$oneClickInscriptionInput->username = $this->WASHITA_USERNAME;
		$oneClickInscriptionInput->email = $this->WASHITA_EMAIL;
		$oneClickInscriptionInput->responseURL = $this->ONECLICK_URL_INSCRIPTION;
		$oneClickInscriptionResponse = $oneClickService->initInscription(array(
			"arg0" => $oneClickInscriptionInput
		));
		$xmlResponse = $oneClickService->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, $this->TBK_SERVER_CERT);
		//Esto valida si el mensaje está firmado por Transbank
		$soapValidation->getValidationResult(); 
		//Esto obtiene el resultado de la operación
		$oneClickInscriptionOutput = $oneClickInscriptionResponse->return; 
		$tokenOneClick = $oneClickInscriptionOutput->token; //Token de resultado
		//URL para realizar el post
		$inscriptionURL = $oneClickInscriptionOutput->urlWebpay; 
		
	}
	/** @method void FINISH_INSCRIPTION() this function finish the TC inscription process. */
	function FINISH_INSCRIPTION($tokenOneClick){
		$oneClickService = new OneClickWS();
		$oneClickFinishInscriptionInput = new oneClickFinishInscriptionInput();
		// INGRESAMOS EL TOKEN DEVUELTO POR EL PROCESO INIT_INSCRIPTION
		$oneClickFinishInscriptionInput->token = $tokenOneClick; 
		$oneClickFinishInscriptionResponse = $oneClickService->finishInscription(array(
		"arg0" => $oneClickFinishInscriptionInput
		));
		$xmlResponse = $oneClickService->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, $this->TBK_CERT_FILE);
		// Si la firma es válida
		$oneClickFinishInscriptionOutput = $oneClickFinishInscriptionResponse->return;
		// Datos de resultado de la inscripción OneClick
		$responseCode = $oneClickFinishInscriptionOutput->responseCode;
		$authCode = $oneClickFinishInscriptionOutput->authCode;
		$creditCardType = $oneClickFinishInscriptionOutput->creditCardType;
		$last4CardDigits = $oneClickFinishInscriptionOutput->last4CardDigits;
		$tbkUser = $oneClickFinishInscriptionOutput->tbkUser;
		// INGRESAMOS LOS CAMPOS A LA BASE DE DATOS
		$TC['ID_USER']
	}
	/** @method void AUTHORIZE() this function authorice a transaction with transbank oneclick. */
	function AUTHORIZE(){
		$oneClickService = new OneClickWS();
		$oneClickInscriptionInput = new oneClickInscriptionInput();
		$oneClickPayInput = new oneClickPayInput();
		// CREAMOS LA PREORDEN
		// VERIFICAMOS CON TRANSBANK
		$oneClickPayInput->amount = <monto de pago>;
		$oneClickPayInput->buyOrder = <orden de compra>;
		$oneClickPayInput->tbkUser = <identificador de usuario entregado
		en el servicio finishInscription>;
		$oneClickPayInput->username = <identificador de usuario del comercio>;
		$oneClickauthorizeResponse = $oneClickService->authorize(array(
			"arg0" => $oneClickPayInput
		));
		$xmlResponse = $oneClickService->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, $this->TBK_CERT_FILE);
		$oneClickPayOutput = $oneClickauthorizeResponse->return;
		//Resultado de la autorización
		$authorizationCode = $oneClickPayOutput->authorizationCode;
		$creditCardType = $oneClickPayOutput->creditCardType;
		$last4CardDigits = $oneClickPayOutput->last4CardDigits;
		$responseCode = $oneClickPayOutput->responseCode;
		$transactionId = $oneClickPayOutput->transactionId;
		// CREAMOS LA ORDEN EN LA BASE DE DATOS
	}
	/** @method void REVERSE() this function reverse a transaction when it was processed. */
	function REVERSE(){
		$oneClickService = new OneClickWS();
		$oneClickReverseInput = new oneClickReverseInput();
		$buyOrder = <orden de compra de la transacción>;
		$oneClickReverseInput->buyorder= $buyOrder;
		$codeReverseOneClickResponse = $oneClickService->codeReverseOneClick(array(
			"arg0" => $oneClickReverseInput
		));
		$oneClickReverseOutput = $codeReverseOneClickResponse->return;
		$xmlResponse = $oneClickService->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, $this->TBK_CERT_FILE);
		// Si la firma es válida
		// INDICA SI LA TRANSACCION SE REVERSO
		$reversed = $oneClickReverseOutput->reversed; 
		// OBTENEMOS EL CODIGO DE REVERSA
		$reverseCode = $oneClickReverseOutput->reverseCode;  
		// MODIFICAMOS LA BASE DE DATOS
	}
	/** @method void REMOVE_INSCRIPTION() this function remove an TC inscription. */
	function REMOVE_INSCRIPTION(){
		$oneClickService = new OneClickWS();
		$oneClickRemoveUserInput = new oneClickRemoveUserInput();
		$tbkUser = <identificador de usuario entregado en el servicio finishInscription>;
		$commerceUser = <identificador de usuario del comercio>;
		$oneClickRemoveUserInput->tbkUser = $tbkUser;
		$oneClickRemoveUserInput->username = $commerceUser;
		$removeUserResponse = $oneClickService->removeUser(array(
			"arg0" => $oneClickRemoveUserInput
		));
		$xmlResponse = $oneClickService->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, SERVER_CERT);
		//Si la firma es válida
		// Valor booleano que indica si el usuario fue removido.
		$removeUserResponse->return;
		// REMOVEMOS DE LA BASE DE DATOS
	}
	/** @method void GETUSERPARAM() this function finish the TC inscription process. */
	function GETUSERPARAM(){
		if($_POST){
			$USER = WashitaUser::CurrentUser();
			$this->WASHITA_USERNAME = $_POST['username'];
			$this->WASHITA_EMAIL = $_POST['email'];
			return;
		}
		throw new Exception("The user fields not are post", 44);
	}
	/** @method void FINISH_INSCRIPTION() this function finish the TC inscription process. */
	function CHECKCONFIG(){
		if(!isset($this->TBK_SERVER_CERT) OR empty($this->TBK_SERVER_CERT)) throw new Exception("The TBK_SERVER_CERT_FILE is not set", 1);
		if(!isset($this->TBK_PRIVATE_KEY) OR empty($this->TBK_PRIVATE_KEY)) throw new Exception("The TBK_PRIVATE_KEY_WS is not set", 2);
		if(!isset($this->TBK_CERT_FILE) OR empty($this->TBK_CERT_FILE)) throw new Exception("The TBK_SERVER_CERT_FILE is not set", 3);
	}
}
?>
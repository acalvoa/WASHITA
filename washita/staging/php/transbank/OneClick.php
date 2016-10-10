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
class OneClick extends MySQLDB{
	private $TBK_SERVER_CERT;
	private $TBK_PRIVATE_KEY;
	private $TBK_CERT_FILE;
	private $WASHITA_USERNAME;
	private $WASHITA_EMAIL;
	private $ONECLICK_URL_INSCRIPTION;
	private $TBK_SUCCESS;
	private $TBK_FAIL;
	private $TBK_PROD_MODE;
	private $TBK_LOGPATH;
	private $TBK_SESSION;
	function __construct(){
		parent::__construct($GLOBALS["DBServer"],$GLOBALS["DBUser"],$GLOBALS["DBPass"],$GLOBALS["DBName"]);
		$this->TBK_SERVER_CERT = $GLOBALS['TBK_CERT_FILE_WS'];
		$this->TBK_PRIVATE_KEY = $GLOBALS['TBK_PRIVATE_KEY_WS'];
		$this->TBK_CERT_FILE = $GLOBALS['TBK_SERVER_CERT_FILE'];
		$this->ONECLICK_URL_INSCRIPTION = $GLOBALS['ONECLICK_URL_INSCRIPTION'];
		$this->TBK_SUCCESS = $GLOBALS["TBK_SUCCESS"];
		$this->TBK_FAIL = $GLOBALS["TBK_FAIL"];
		$this->TBK_PROD_MODE = $GLOBALS["WSH_PROD_MODE"];
		$this->TBK_LOGPATH = $GLOBALS["LOG_PATH"];
		$this->CHECKCONFIG();
	}
	function INIT_INSCRIPTION($ws = false){
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
		$retorno['token'] = $oneClickInscriptionOutput->token; //Token de resultado
		$retorno['url'] = $oneClickInscriptionOutput->urlWebpay;
		if($ws) die(json_encode($retorno));
		//URL para realizar el post
		printf('<form action="%s" name="frm" method="post">', $retorno['url']);
		printf('<input type="hidden" name="TBK_TOKEN" value="%s"/>', $retorno['token']);
		echo "</form>";
		echo '<script type="text/javascript"> document.frm.submit(); </script>';
		
	}
	/** @method void FINISH_INSCRIPTION() this function finish the TC inscription process. */
	function FINISH_INSCRIPTION($tokenOneClick,$ws = false){
		if(!isset($tokenOneClick)) throw new Exception("The token not are provided", 1);
		$this->GETUSERPARAM();
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
		if($responseCode == 0){
			// INGRESAMOS LOS CAMPOS A LA BASE DE DATOS
			$TC['ID_USER'] = $this->WASHITA_USERNAME;
			$TC['AUTH_CODE'] = $oneClickFinishInscriptionOutput->authCode;
			$TC['CREDIT_CARD_TYPE'] = $oneClickFinishInscriptionOutput->creditCardType;
			$TC['LAST4NUMBER'] = $oneClickFinishInscriptionOutput->last4CardDigits;
			$TC['TBK_USER'] = $oneClickFinishInscriptionOutput->tbkUser;
			$result = $this->INSERT($TC,'TBK_OC_REGISTER_TC');
			if(!$result){
				throw new Exception("The TBK_USER REGISTER ERROR - ERROR IN INSERT OPERATION", 1);
			}
			if($ws) die(json_encode($TC));
			header('Location: /process.php');
		}
		else
		{
			header('Location: /process.php?TC_INS=fail');
		}
	}
	/** @method void AUTHORIZE() this function authorice a transaction with transbank oneclick. */
	function AUTHORIZE($TBK_USER, $ws = false){
		$this->GETUSERPARAM();
		if(!isset($this->WASHITA_USERNAME)) throw new Exception("The USER is not loged is not set", 1);
		$this->GENERATE_SESION();
		$this->LOG("#######################\nIniciamos la transaccion: ".$this->TBK_SESSION);
		// GENERATE THE PREORDER
		$PREORDER = new OrderGenerator($this->WASHITA_USERNAME);
		$PREORDER->PROCESS_FIELDS();
		$ID_PREORDER = $PREORDER->CREATE_PRE_ORDER();
		$this->LOG("Preorden creada: ".$ID_PREORDER.", Redirigiendo");
		if($this->REG_TRANS($ID_PREORDER,$PREORDER->GET_PRICE()."00")){
			// LLAMAMOS LOS WEBSERVICES
			$oneClickService = new OneClickWS();
			$oneClickInscriptionInput = new oneClickInscriptionInput();
			$oneClickPayInput = new oneClickPayInput();
			// CREAMOS LA PREORDEN
			// VERIFICAMOS CON TRANSBANK
			$oneClickPayInput->amount = $PREORDER->GET_PRICE();
			$oneClickPayInput->buyOrder = $ID_PREORDER;
			$oneClickPayInput->tbkUser = $TBK_USER;
			$oneClickPayInput->username = $this->WASHITA_USERNAME;
			$oneClickauthorizeResponse = $oneClickService->authorize(array(
				"arg0" => $oneClickPayInput
			));
			$xmlResponse = $oneClickService->soapClient->__getLastResponse();
			$soapValidation = new SoapValidation($xmlResponse, $this->TBK_CERT_FILE);
			$oneClickPayOutput = $oneClickauthorizeResponse->return;
			//Resultado de la autorización
			$responseCode = $oneClickPayOutput->responseCode;
			if($responseCode == 0){
				$authorizationCode = $oneClickPayOutput->authorizationCode;
				$creditCardType = $oneClickPayOutput->creditCardType;
				$last4CardDigits = $oneClickPayOutput->last4CardDigits;
				$transactionId = $oneClickPayOutput->transactionId;
				// CREAMOS LA ORDEN EN LA BASE DE DATOS
				$this->LOG("Creamos la nueva orden de trabajo. Rescatamos el registro");
				echo $ID_PREORDER;
				$preorder['TBK_ODC'] = $ID_PREORDER;
				$order_param = $this->FIRST('TBK_PREORDER', $preorder);
				unset($order_param['TBK_ODC']);
				unset($order_param['ID_ODC']);
				unset($order_param['ID_USER']);
				$this->LOG("Creamos la nueva orden de trabajo. Creamos el registro");
				$order = $this->INSERT($order_param,"orders");
				$TBK_ORDER['WASHITA_ORDER'] = $GLOBALS["OrdersNumberStart"] + $order;
				$TBK_ORDER['PAYMENT_STATUS'] = 1;
				$order_resp = $this->UPDATE($TBK_ORDER,$preorder,"TBK_TRANSACTIONS");
				if(!($order_resp)){
					$this->REVERSE($ID_PREORDER);
					$where['ID'] = $order;
					$this->DELETE($where,"orders");
					$in['REVERSED'] = 1;
					$order_resp = $this->UPDATE($in,$preorder,"TBK_TRANSACTIONS");
					if($ws){
						$retorno['STATUS'] = 0;
						$retorno['TBK_ODC'] = $ID_PREORDER;
						die(json_encode($retorno));
					}
					else
					{
						printf('<form action="%s" name="frm" method="post">', $this->TBK_FAIL);
						printf('<input type="hidden" name="TBK_ODC" value="%s"/>', $ID_PREORDER);
						echo "</form>";
						echo '<script type="text/javascript"> document.frm.submit(); </script>';
						die();
					}
				}
				$ORDER_FINAL['ORDER_NUMBER'] = $TBK_ORDER['WASHITA_ORDER'];
				$ORDER_FINAL['PAYMENT_STATUS'] = 1;
				$ORDER_WHERE['ID']  = $order;
				$order_result = $this->UPDATE($ORDER_FINAL,$ORDER_WHERE,"orders");
				if(!($order_result)){
					$this->REVERSE($ID_PREORDER);
					$where['ID'] = $order;
					$this->DELETE($where,"orders");
					$in['REVERSED'] = 1;
					$order_resp = $this->UPDATE($in,$preorder,"TBK_TRANSACTIONS");
					if($ws){
						$retorno['STATUS'] = 0;
						$retorno['TBK_ODC'] = $ID_PREORDER;
						die(json_encode($retorno));
					}
					else
					{
						printf('<form action="%s" name="frm" method="post">', $this->TBK_FAIL);
						printf('<input type="hidden" name="TBK_ODC" value="%s"/>', $ID_PREORDER);
						echo "</form>";
						echo '<script type="text/javascript"> document.frm.submit(); </script>';
						die();
					}
				}
				 // SEND EMAIL
		        $mailService = new MailService();
	        	$mailService->SendNotification($TBK_ORDER['WASHITA_ORDER']);
	        	if($ws){
					$retorno['STATUS'] = 1;
					$retorno['TBK_ODC'] = $ID_PREORDER;
					$retorno['TBK_TRANS_ID'] = $transactionId;
					$retorno['TBK_AUTH_CODE'] = $authorizationCode;
					$retorno['TBK_TC_DIGIT'] = $last4CardDigits;
					$retorno['TBK_TC_TYPE'] = $creditCardType;
					die(json_encode($retorno));
				}
				else
				{
					printf('<form action="%s" name="frm" method="post">', $this->TBK_SUCCESS);
					printf('<input type="hidden" name="TBK_ODC" value="%s"/>', $ID_PREORDER);
					printf('<input type="hidden" name="TBK_AMOUNT" value="%s"/>', $PREORDER->GET_PRICE());
					printf('<input type="hidden" name="TBK_AUTH_CODE" value="%s"/>', $authorizationCode);
					printf('<input type="hidden" name="TBK_TC_DIGIT" value="%s"/>', $last4CardDigits);
					printf('<input type="hidden" name="TBK_TC_TYPE" value="%s"/>', $creditCardType);
					echo "</form>";
					echo '<script type="text/javascript"> document.frm.submit(); </script>';
					die();
				}
			}
			else
			{
				if($ws){
					$retorno['STATUS'] = 0;
					$retorno['TBK_ODC'] = $ID_PREORDER;
					die(json_encode($retorno));
				}
				else
				{
					printf('<form action="%s" name="frm" method="post">', $this->TBK_FAIL);
					printf('<input type="hidden" name="TBK_ODC" value="%s"/>', $ID_PREORDER);
					echo "</form>";
					echo '<script type="text/javascript"> document.frm.submit(); </script>';
					die();
				}
			}
		}
		else
		{
			throw new Exception("Database transaction register has problems", 4);
		}
		

	}
	/** @method void REVERSE() this function reverse a transaction when it was processed. */
	function REVERSE($ODC){
		$oneClickService = new OneClickWS();
		$oneClickReverseInput = new oneClickReverseInput();
		$buyOrder = $ODC;
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
	function REMOVE_INSCRIPTION($ODC,$TBK_USER){
		$this->GETUSERPARAM();
		$oneClickService = new OneClickWS();
		$oneClickRemoveUserInput = new oneClickRemoveUserInput();
		$tbkUser = $TBK_USER;
		$commerceUser = $this->WASHITA_USERNAME;
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
		$USER = WashitaUser::CurrentUser();
		$this->WASHITA_USERNAME = $USER->Id;
		$this->WASHITA_EMAIL = $USER->Email;
		return;
	}
	/** @method void FINISH_INSCRIPTION() this function finish the TC inscription process. */
	function CHECKCONFIG(){
		if(!isset($this->TBK_SERVER_CERT) OR empty($this->TBK_SERVER_CERT)) throw new Exception("The TBK_SERVER_CERT_FILE is not set", 1);
		if(!isset($this->TBK_PRIVATE_KEY) OR empty($this->TBK_PRIVATE_KEY)) throw new Exception("The TBK_PRIVATE_KEY_WS is not set", 2);
		if(!isset($this->TBK_CERT_FILE) OR empty($this->TBK_CERT_FILE)) throw new Exception("The TBK_SERVER_CERT_FILE is not set", 3);
	}
	public function PROVIDERS(){
		$this->GETUSERPARAM();
		$where['ID_USER'] = $this->WASHITA_USERNAME;
		return $this->GET('TBK_OC_REGISTER_TC', $where);
	}
	public static function GETPROVIDERS(){
		$oneclick = new OneClick();
		return $oneclick->PROVIDERS();
	}
	/** @method void LOG(string $message) this function finalice the transaction with transbank and close the process */
	public function LOG($message){
		if(!$this->TBK_PROD_MODE){
			$logfile = $this->TBK_LOGPATH."/log.txt";
			$fp=fopen($logfile,"a+");
			fwrite($fp, "\n".$message);
			fclose($fp);
		}
	}
	/** @method void REG_TRANS() this function register the transbank transaction */
	public function REG_TRANS($odc, $amount){
		$TRANSACTION = array();
		$TRANSACTION['TBK_SESSION'] = $this->TBK_SESSION;
		$TRANSACTION['TBK_ODC'] = $odc;
		$TRANSACTION['TBK_AMOUNT'] = $amount;
		$TRANSACTION['PAYMENT_STATUS'] = 0;
		return $this->INSERT($TRANSACTION,"TBK_TRANSACTIONS");
	}
	/** @method void GENERATE_SESION() this function check the MAC provided by transbank */
	private function GENERATE_SESION(){
		$user_id = $this->WASHITA_USERNAME;
		$time_hash = sha1(time());
		$hash = $user_id."@".$time_hash;
		$this->TBK_SESSION = md5($hash);
	}
}
?>
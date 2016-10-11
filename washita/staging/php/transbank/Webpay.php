<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for use the Webpay Plus Kit provided by transbank.
*/
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../hybridauth/WashitaUser.php");
include_once(dirname(__FILE__)."/../MailService.class.php");
require_once(dirname(__FILE__)."/OrdersGenerator.php");
require_once(dirname(__FILE__).'/vendor/webpay/webpay.php' );
require_once("MySQLDB.php");

/**
*	This class is the main class of the Webpay Plus Process.
*/
class Webpay extends MySQLDB{
	/** @var string $TBK_SESSION Indicate the session used by transbank while the transaction is open */
	private $TBK_SESSION;
	/** @var string $TBK_ODC Indicate the Sell Order of the transaction */
	private $TBK_ODC;
	/** @var string $TBK_ODC Indicate the amount of the transaction with transbank */
	private $TBK_MONTO;
	/** @var string $TBK_ODC Indicate the type of transaction. */
	private $TBK_TIPO_TRANSACCION = NULL;
	/** @var string $TBK_ODC Indicate the success page script */
	private $TBK_SUCCESS = NULL;
	/** @var string $TBK_ODC Indicate the fail page script */
	private $TBK_FAIL = NULL;
	/** @var WashitaUser $USER Indicate the fail page script */
	private $USER;
	/** @var string $TBK_MAC_PATH Indicate the MAC path */
	private $TBK_MAC_PATH = NULL;
	/** @var string $TBK_URL_KIT Indicate the Url of webpay plus kit */
	private $TBK_URL_KIT = NULL;
	/** @var string $TBK_MAC_FILE Indicate the MAC file */
	private $TBK_MAC_FILE = NULL;
	/** @var string $TBK_CHECK_MAC_PATH Indicate the Check MAC cgi file */
	private $TBK_CHECK_MAC_PATH = NULL;
	/** @var boolean $TBK_PROD_MODE Indicate if the system is in PRODUCTION MODE */
	private $TBK_PROD_MODE = FALSE;
	/** @var string $TBK_LOGPATH Indicate the log path when the system is not in PRODUCTION MODE */
	private $TBK_LOGPATH;
	/** @var string $TBK_SERVER_CERT Indicate the path of TRANSBANK CERT  */
	private $TBK_SERVER_CERT;
	/** @var string $TBK_PRIVATE_KEY Indicate the path of PRIVATE KEY WS */
	private $TBK_PRIVATE_KEY;
	/** @var string $TBK_CERT_FILE Indicate the path of PUBLIC CERT FILE WS  */
	private $TBK_CERT_FILE;
	/** @var string $TBK_CERT_FILE Indicate the commerce code for ws (Only WS)*/
	private $TBK_COMMERCE_CODE;
	/** @var string $TBK_WEBPAY_RESULT Indicate the result link for webpay ws (Only WS)*/
	private $TBK_WEBPAY_RESULT;
	/** @var string $TBK_WEBPAY_END Indicate the end link for webpay ws (Only WS)*/
	private $TBK_WEBPAY_END;

	/** @method __construct() represent the main constructor of class. this method get the database values from global configuration. */
	function __construct(){
		parent::__construct($GLOBALS["DBServer"],$GLOBALS["DBUser"],$GLOBALS["DBPass"],$GLOBALS["DBName"]);
		$this->TBK_MAC_PATH = $GLOBALS["TBK_MAC_PATH"];
		$this->TBK_SUCCESS = $GLOBALS["TBK_SUCCESS"];
		$this->TBK_FAIL = $GLOBALS["TBK_FAIL"];
		$this->TBK_URL_KIT = $GLOBALS["TBK_URL_KIT"];
		$this->TBK_TIPO_TRANSACCION = $GLOBALS["TBK_TIPO_TRANSACCION"];
		$this->TBK_CHECK_MAC_PATH = $GLOBALS["TBK_CHECK_MAC_PATH"];
		$this->TBK_PROD_MODE  = $GLOBALS["WSH_PROD_MODE"];
		$this->TBK_LOGPATH = $GLOBALS["LOG_PATH"];
		$this->TBK_SERVER_CERT = $GLOBALS['TBK_CERT_FILE_WS'];
		$this->TBK_PRIVATE_KEY = $GLOBALS['TBK_PRIVATE_KEY_WS'];
		$this->TBK_CERT_FILE = $GLOBALS['TBK_SERVER_CERT_FILE'];
		$this->TBK_COMMERCE_CODE = $GLOBALS['TBK_COMMERCE_CODE'];
		$this->TBK_WEBPAY_RESULT = $GLOBALS['TBK_WEBPAY_RESULT'];
		$this->TBK_WEBPAY_END = $GLOBALS['TBK_WEBPAY_END'];
		$this->VERIFY_CONFIG();
	}
	/** @method void VERIFY_CONFIG() this function verify when the config was set. */
	private function VERIFY_CONFIG(){
		if(!isset($this->TBK_MAC_PATH)) throw new Exception("The TBK_MAC_PATH is not set", 1);
		if(!isset($this->TBK_SUCCESS)) throw new Exception("The TBK_SUCCESS is not set", 2);
		if(!isset($this->TBK_FAIL)) throw new Exception("The TBK_FAIL is not set", 3);
		if(!isset($this->TBK_URL_KIT)) throw new Exception("The TBK_URL_KIT is not set", 4);
		if(!isset($this->TBK_TIPO_TRANSACCION)) throw new Exception("The TBK_TIPO_TRANSACCION is not set", 4);
		if(!isset($this->TBK_CHECK_MAC_PATH)) throw new Exception("The TBK_CHECK_MAC_PATH is not set", 4);
	}
	/** @method void START_TRANS() this function start the transbank transaction */
	public function START_TRANS(){
		// FIRST WE WILL GENERATE A SESION CODE
		$this->USER = WashitaUser::CurrentUser();
		if(!isset($this->USER)) throw new Exception("The USER is not loged is not set", 1);
		$this->GENERATE_SESION();
		$this->LOG("#######################\nIniciamos la transaccion: ".$this->TBK_SESSION);
		// GENERATE THE PREORDER
		$PREORDER = new OrderGenerator($this->USER->Id);
		$PREORDER->PROCESS_FIELDS();
		$ID_PREORDER = $PREORDER->CREATE_PRE_ORDER();
		$this->LOG("Preorden creada: ".$ID_PREORDER.", Redirigiendo");
		if($this->REG_TRANS($ID_PREORDER,$PREORDER->GET_PRICE()."00")){
			printf('<form action="%s" name="frm" method="post">', $this->TBK_URL_KIT);
			printf('<input type="hidden" name="TBK_TIPO_TRANSACCION" value="%s"/>', $this->TBK_TIPO_TRANSACCION);
			printf('<input type="hidden" name="TBK_MONTO" value="%s"/>', $PREORDER->GET_PRICE()."00");
			printf('<input type="hidden" name="TBK_ORDEN_COMPRA" value="%s"/>', $ID_PREORDER);
			printf('<input type="hidden" name="TBK_ID_SESION" value="%s"/>', $this->TBK_SESSION);
			printf('<input type="hidden" name="TBK_URL_EXITO" value="%s"/>', $this->TBK_SUCCESS);
			printf('<input type="hidden" name="TBK_URL_FRACASO" value="%s"/>', $this->TBK_FAIL);
			echo "</form>";
			echo '<script type="text/javascript"> document.frm.submit(); </script>';
		}
		else
		{
			throw new Exception("Database transaction register has problems", 4);
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
	/** @method void VERIFY() this function verify the result of transbank in the two-ways transbank verify process */
	public function VERIFY(){
		if(!$_POST) throw new Exception("No are a transbank transaction", 1);
		$this->LOG("Comienza la verificacion de sesion: ".$_POST["TBK_ID_SESION"]);
		$TBK_RESPUESTA = $_POST["TBK_RESPUESTA"];
		$this->TBK_ODC = $_POST["TBK_ORDEN_COMPRA"];
		$this->TBK_MONTO =$_POST["TBK_MONTO"];
		$this->TBK_SESSION = $_POST["TBK_ID_SESION"];
		//VERIFICAMOS LA RESPUESTA DE TRANSBANK
		$this->CHECK_TBK($TBK_RESPUESTA);
		//PRIMERO VERIFICACION DE LO CAMPOS CON EL REGISTRO
		$this->CHECK_TOKEN();
		// CREAMOS LA MAC
		$this->GENERATE_MAC();
		// VERIFICAMOS LA MAC
		$this->CHECK_MAC();
		// VERIFICAMOS LA MAC
		$this->FINALICE();
		// FINALIZAMOS SI NO HAY ERRORES
		die('ACEPTADO');

	}
	/** @method void CHECK_TOKEN() this function check the transaction with the database registers */
	private function CHECK_TOKEN(){
		$CHECK = array();
		$CHECK['TBK_ODC'] = $this->TBK_ODC;
		$CHECK['TBK_AMOUNT'] = $this->TBK_MONTO;
		$CHECK['TBK_SESSION'] = $this->TBK_SESSION;
		$this->LOG("Verificamos el token: ".json_encode($CHECK));
		//VERIFICAMOS
		$this->GET('TBK_TRANSACTIONS', $CHECK);
		$this->LOG("Resultado del token: ".$this->NUMROWS());
		if(!($this->NUMROWS() > 0)){
			die('RECHAZADO');
		}
	}
	/** @method void GENERATE_MAC() this function check the MAC provided by transbank */
	private function GENERATE_MAC(){
		$this->TBK_MAC_FILE = $this->TBK_MAC_PATH."/MAC01Normal".$this->TBK_SESSION.".txt";
		$this->LOG("Generamos el MAC file: ".$this->TBK_MAC_FILE);
		$fp=fopen($this->TBK_MAC_FILE,"wt");
		while(list($key, $val)=each($_POST)){
			fwrite($fp, "$key=$val&");
		}
		fclose($fp);
		$this->LOG("Archivo MAC generado");
	}
	/** @method void GENERATE_SESION() this function check the MAC provided by transbank */
	private function GENERATE_SESION(){
		$user_id = $this->USER->Id;
		$time_hash = sha1(time());
		$hash = $user_id."@".$time_hash;
		$this->TBK_SESSION = md5($hash);
	}
	/** @method void CHECK_MAC() this function check the MAC provided by transbank */
	private function CHECK_MAC(){
		$this->LOG("Checkeamos el MAC file: ".$this->TBK_MAC_FILE);
		$cmdline = $this->TBK_CHECK_MAC_PATH." ".$this->TBK_MAC_FILE;
		exec($cmdline, $result, $retint);
		$this->LOG("Respuesta del MAC check: ".$result[0]);
		if($result[0] != "CORRECTO"){
			die('RECHAZADO');
		}
	}
	/** @method void CHECK_TBK() this function finalice the transaction with transbank and close the process */
	public function CHECK_TBK($respuesta){
		$this->LOG("Verificamos la respuesta de transbank ".$respuesta);
		if(!($respuesta == "0")){
			die('RECHAZADO');
		}
	}
	/** @method void FINALICE() this function finalice the transaction with transbank and close the process */
	public function FINALICE(){
		$this->LOG("Creamos la nueva orden de trabajo. Rescatamos el registro");
		$preorder['TBK_ODC'] = $this->TBK_ODC;
		$order_param = $this->FIRST('TBK_PREORDER', $preorder);
		unset($order_param['TBK_ODC']);
		unset($order_param['ID_USER']);
		$this->LOG("Creamos la nueva orden de trabajo. Creamos el registro");
		$order = $this->INSERT($order_param,"orders");
		$TBK_ORDER['WASHITA_ORDER'] = $GLOBALS["OrdersNumberStart"] + $order;
		$TBK_ORDER['PAYMENT_STATUS'] = 1;
		$order_resp = $this->UPDATE($TBK_ORDER,$preorder,"TBK_TRANSACTIONS");
		if(!($order_resp)){
			die('RECHAZADO');
		}
		$ORDER_FINAL['ORDER_NUMBER'] = $TBK_ORDER['WASHITA_ORDER'];
		$ORDER_FINAL['PAYMENT_STATUS'] = 1;
		$ORDER_WHERE['ID']  = $order;
		$order_result = $this->UPDATE($ORDER_FINAL,$ORDER_WHERE,"orders");
		if(!($order_result)){
			die('RECHAZADO');
		}
		 // SEND EMAIL
        $mailService = new MailService();
        $mailService->SendNotification($TBK_ORDER['WASHITA_ORDER']);
	}
	/** @method void START_TRANS_WS() this function start the WS transbank transaction */
	public function START_TRANS_WS(){
		// FIRST WE WILL GENERATE A SESION CODE
		$this->USER = WashitaUser::CurrentUser();
		if(!isset($this->USER)) throw new Exception("The USER is not loged is not set", 1);
		$this->GENERATE_SESION();
		$this->LOG("#######################\nIniciamos la transaccion: ".$this->TBK_SESSION);
		// GENERATE THE PREORDER
		$webpay_settings = array(
			"MODO" => "INTEGRACION",
			"PRIVATE_KEY" => file_get_contents($this->TBK_PRIVATE_KEY),
			"PUBLIC_CERT" => file_get_contents($this->TBK_SERVER_CERT),
			"WEBPAY_CERT" => file_get_contents($this->TBK_CERT_FILE),
			"COMMERCE_CODE" => $this->TBK_COMMERCE_CODE,
			"URL_RETURN" => $this->TBK_WEBPAY_RESULT,
			"URL_FINAL" => $this->TBK_WEBPAY_END,
		);

		$webpay = new WebPaySOAP($webpay_settings); // Crea objeto WebPay
		$webpay = $webpay->getNormalTransaction(); // Crea Transaccion Normal

		$PREORDER = new OrderGenerator($this->USER->Id);
		$PREORDER->PROCESS_FIELDS();
		$ID_PREORDER = $PREORDER->CREATE_PRE_ORDER();
		$this->LOG("Preorden creada: ".$ID_PREORDER.", Redirigiendo");
		if($this->REG_TRANS($ID_PREORDER,$PREORDER->GET_PRICE())){
	 		$result = $webpay->initTransaction($PREORDER->GET_PRICE(), $this->TBK_SESSION, $ID_PREORDER);
			$webpay_token = $result["token_ws"];
			
			printf('<form action="%s" name="frm" method="post">', $result["url"]);
			printf('<input type="hidden" name="token_ws" value="%s"/>', $webpay_token);
			echo "</form>";
			echo '<script type="text/javascript"> document.frm.submit(); </script>';
		}
		else
		{
			throw new Exception("Database transaction register has problems", 4);
		}
	}
	/** @method void START_END_WS() this function start the WS transbank transaction */
	public function RESULT_WEBPAY_WS(){
		// FIRST WE WILL GENERATE A SESION CODE
		$this->USER = WashitaUser::CurrentUser();
		if(!isset($this->USER)) throw new Exception("The USER is not loged is not set", 1);
		$webpay_settings = array(
			"MODO" => "INTEGRACION",
			"PRIVATE_KEY" => file_get_contents($this->TBK_PRIVATE_KEY),
			"PUBLIC_CERT" => file_get_contents($this->TBK_SERVER_CERT),
			"WEBPAY_CERT" => file_get_contents($this->TBK_CERT_FILE),
			"COMMERCE_CODE" => $this->TBK_COMMERCE_CODE,
			"URL_RETURN" => $this->TBK_WEBPAY_RESULT,
			"URL_FINAL" => $this->TBK_WEBPAY_END,
		);
		$webpay = new WebPaySOAP($webpay_settings); // Crea objeto WebPay
		$webpay_token = $_POST["token_ws"];
		$webpay = $webpay->getNormalTransaction(); // Crea Transaccion Normal
		$result = $webpay->getTransactionResult($webpay_token); 
		// Verificamos resultado del pago
		if ($result->detailOutput->responseCode===0) {
			echo "<pre>";
			print_r($result);
			echo "</pre>";
			$message = "Pago ACEPTADO por webpay (se deben guardatos para mostrar voucher)";
			$next_page = $result->urlRedirection;
			$next_page_title = "Finalizar Pago";
			echo "<br>".$message;
		} else {
			print_r($result);
			$message = "Pago RECHAZADO por webpay - ".utf8_decode($result->detailOutput->responseDescription);
                        $next_page='';
            echo "<br>".$message;
		}
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
}
?>

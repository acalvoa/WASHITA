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
require_once(dirname(__FILE__)."/OrdersGenerator.php");
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

	/** @method __construct() represent the main constructor of class. this method get the database values from global configuration. */
	function __construct(){
		parent::__construct($GLOBALS["DBServer"],$GLOBALS["DBUser"],$GLOBALS["DBPass"],$GLOBALS["DBName"]);
		$this->USER = WashitaUser::CurrentUser();
		$this->TBK_MAC_PATH = $GLOBALS["TBK_MAC_PATH"];
		$this->TBK_SUCCESS = $GLOBALS["TBK_SUCCESS"];
		$this->TBK_FAIL = $GLOBALS["TBK_FAIL"];
		$this->TBK_URL_KIT = $GLOBALS["TBK_URL_KIT"];
		$this->TBK_TIPO_TRANSACCION = $GLOBALS["TBK_TIPO_TRANSACCION"];
		// $this->VERIFY_CONFIG();
	}
	/** @method void VERIFY_CONFIG() this function verify when the config was set. */
	private function VERIFY_CONFIG(){
		if(!isset($this->USER)) throw new Exception("The USER is not loged is not set", 1);
		if(!isset($this->TBK_MAC_PATH)) throw new Exception("The TBK_MAC_PATH is not set", 1);
		if(!isset($this->TBK_SUCCESS)) throw new Exception("The TBK_SUCCESS is not set", 2);
		if(!isset($this->TBK_FAIL)) throw new Exception("The TBK_FAIL is not set", 3);
		if(!isset($this->TBK_URL_KIT)) throw new Exception("The TBK_URL_KIT is not set", 4);
	}
	/** @method void START_TRANS() this function start the transbank transaction */
	public function START_TRANS(){
		// FIRST WE WILL GENERATE A SESION CODE
		$this->GENERATE_SESION();
		// GENERATE THE PREORDER
		$PREORDER = new OrderGenerator($this->USER->Id);
		$PREORDER->PROCESS_FIELDS();
		$ID_PREORDER = $PREORDER->CREATE_PRE_ORDER();
		echo $this->REG_TRANS($ID_PREORDER,$PREORDER->GET_PRICE()."00");
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
	/** @method void START_TRANS() this function verify the result of transbank in the two-ways transbank verify process */
	public function VERIFY(){
		
	}
	/** @method void START_TRANS() this function check the transaction with the database registers */
	private function CHECK_TOKEN(){
		
	}
	/** @method void START_TRANS() this function check the MAC provided by transbank */
	private function GENERATE_MAC(){
		$fp=fopen($filename_txt,"wt");
		while(list($key, $val)=each($_POST)){
			fwrite($fp, "$key=$val&");
		}
		fclose($fp);
	}
	/** @method void START_TRANS() this function check the MAC provided by transbank */
	private function GENERATE_SESION(){
		$user_id = $this->USER->Id;
		$time_hash = sha1(time());
		$hash = $user_id."@".$time_hash;
		$this->TBK_SESSION = md5($hash);
	}
	/** @method void START_TRANS() this function check the MAC provided by transbank */
	private function CHECK_MAC(){
		
	}
	/** @method void START_TRANS() this function finalice the transaction with transbank and close the process */
	public function FINALICE(){
		
	}
}
?>

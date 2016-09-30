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
	private $TBK_TIPO_TRANSACCION;
	/** @var string $TBK_ODC Indicate the success page script */
	private $TBK_SUCCESS;
	/** @var string $TBK_ODC Indicate the fail page script */
	private $TBK_FAIL;
	/** @var WashitaUser $USER Indicate the fail page script */
	private $USER;
	/** @var string $TBK_MAC_PATH Indicate the MAC path */
	private $TBK_MAC_PATH;
	/** @method __construct() represent the main constructor of class. this method get the database values from global configuration. */
	function __construct(){
		parent::__construct($GLOBALS["DBServer"],$GLOBALS["DBUser"],$GLOBALS["DBPass"],$GLOBALS["DBName"]);
		$this->user = WashitaUser::CurrentUser();
		$this->TBK_MAC_PATH = $GLOBALS["TBK_MAC_PATH"];
	}
	/** @method void START_TRANS() this function start the transbank transaction */
	function START_TRANS(){

	}
	/** @method void START_TRANS() this function verify the result of transbank in the two-ways transbank verify process */
	function VERIFY(){
		
	}
	/** @method void START_TRANS() this function check the transaction with the database registers */
	function CHECK_TOKEN(){
		
	}
	/** @method void START_TRANS() this function check the MAC provided by transbank */
	function GENERATE_MAC(){
		
	}
	/** @method void START_TRANS() this function check the MAC provided by transbank */
	function CHECK_MAC(){
		
	}
	/** @method void START_TRANS() this function finalice the transaction with transbank and close the process */
	function FINALICE(){
		
	}
}
?>

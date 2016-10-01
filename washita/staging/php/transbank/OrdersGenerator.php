<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for use the Webpay Plus Kit provided by transbank.
*/
require_once("MySQLDB.php");
include_once(dirname(__FILE__)."/../_helpers.php");
include_once(dirname(__FILE__)."/../Price.class.php");
include_once(dirname(__FILE__)."/../WashType.enum.php");
include_once(dirname(__FILE__)."/../PickupTime.class.php");
include_once(dirname(__FILE__)."/../OrderWashItemLine.class.php");
include_once(dirname(__FILE__)."/../OrderCustomItemLine.class.php");
/**
*	This class is the main class of the OrderGenerator Process.
*/
class OrderGenerator extends MySQLDB{
	/* @var bolean $AUTORIZE*/
	private $AUTORIZE = false;
	/* @var PriceParameters $PRICE_OBJ*/
	private $PRICE_OBJ = NULL;
	/* @var Object $TBK_ORDER*/
	private $PRICE = 0;
	/* @var Object $TBK_ORDER*/
	private $TBK_ORDER = NULL;
	/* @var integer $USER_ID */
	private $USER_ID;
	/* @method __construct This is the constructor of the class */
	function __construct($user){
		parent::__construct($GLOBALS["DBServer"],$GLOBALS["DBUser"],$GLOBALS["DBPass"],$GLOBALS["DBName"]);
		$this->USER_ID = $user;
	}
	/* @method integer CREATE_PRE_ORDER() This method create a preorder for pay with the process */
	function CREATE_PRE_ORDER(){
		if($this->TBK_ORDER == NULL) throw new Exception("No have data in TBK_ORDER");
		return $this->INSERT($this->TBK_ORDER,"TBK_PREORDER");
	}
	/* @method integer CREATE_ORDER() This method create a order to finalice the order */
	function CREATE_ORDER(){

	}
	/* @method void PROCESS_FIELDS() This method create a preorder for pay with the process */
	function PROCESS_FIELDS(){
		$TBK_ORDER = array();
		$TBK_ORDER['ID_USER'] =  $this->USER_ID; 
		$TBK_ORDER['NAME'] =  GetPostNoLongerThan('name', 256); 
		$TBK_ORDER['CITY_AREA_ID'] = GetPostNoLongerThan('city_area_id',3);
	    $TBK_ORDER['ADDRESS'] =  GetPostNoLongerThan('address', 1024); 
	    $TBK_ORDER['EMAIL'] = GetPostNoLongerThan('email', 124); 
	    $TBK_ORDER['PHONE'] = GetPostNoLongerThan('whatsapp', 20); 
	    $TBK_ORDER['DISCOUNT_COUPON'] = GetPostNoLongerThan('discount_coupon', 30);
	    $TBK_ORDER['PICKUP_FROM'] = PickupTime::CreatePickupTimeFromString(GetPostNoLongerThan('pickup_datetime',50),'d/m/Y H:i','|')->from->format("Y-m-d H:i:s");
	    $TBK_ORDER['PICKUP_TILL'] = PickupTime::CreatePickupTimeFromString(GetPostNoLongerThan('pickup_datetime',50),'d/m/Y H:i','|')->from->format("Y-m-d H:i:s");
        $TBK_ORDER['DROPOFF_FROM'] = PickupTime::CreatePickupTimeFromString(GetPostNoLongerThan('dropoff_datetime',50),'d/m/Y H:i','|')->from->format("Y-m-d H:i:s");
        $TBK_ORDER['DROPOFF_TILL'] = PickupTime::CreatePickupTimeFromString(GetPostNoLongerThan('dropoff_datetime',50),'d/m/Y H:i','|')->from->format("Y-m-d H:i:s");
        $TBK_ORDER['COMMENT'] = GetPostNoLongerThan('comment', 3000);
    	$TBK_ORDER['WASH_TYPE'] = WashType::ConvertFromPost(GetPostNoLongerThan('laundry_option', 200));
	    $this->AUTORIZE = GetBooleanPost('terms');
	    
	    $this->PRICE_OBJ = new PriceParameters();
	    $this->PRICE_OBJ->kilo = 0;//default
	    $this->PRICE_OBJ->WashType = $TBK_ORDER['WASH_TYPE'];

	    $ironingItemLines=[];
	    if($this->PRICE_OBJ->WashType == WashType::WashingAndIroning){

	        $ironing_items_post = isset($_POST['ironing_items_post']) ? $_POST['ironing_items_post']: "";
	        $ironingItemLines =  OrderCustomItemLine::ConvertFromPost(WashType::OnlyIroning, $ironing_items_post);
	        $this->PRICE_OBJ->TotalIroningItems = count($ironingItemLines);

	        if(GetBooleanPost('checkbox_washing')){
	            $orderWashitemLines = isset($_POST['washitems']) ? $_POST['washitems']: "";
	            $this->PRICE_OBJ->WashItemLines = OrderWashItemLine::ConvertFromPost($orderWashitemLines);
	            $this->PRICE_OBJ->kilo = GetPost('weight');
	        }
	    }
	    else if($this->PRICE_OBJ->WashType == WashType::DryCleaning){
	        $orderDryCleaningItemLines = isset($_POST['dry_cleaning_items_post']) ? $_POST['dry_cleaning_items_post']: "";
	        $this->PRICE_OBJ->WashItemLines = OrderWashItemLine::ConvertFromPost($orderDryCleaningItemLines);
	        $this->PRICE_OBJ->kilo = GetPost('weight');
	    }
	    // APLICAMOS DESCUENTOS EN CASO DE HABERLOS 
	    $this->PRICE_OBJ->Discount = DiscountCoupon::GetDiscountByCoupon($TBK_ORDER['DISCOUNT_COUPON'], $TBK_ORDER['EMAIL']);
	    // CALCULA OS EL PRECIO
	    $price = Price::DefaultPrice();
        $price_result = $price->CalculatePrice($this->PRICE_OBJ);
        $TBK_ORDER['WEIGHT'] = $this->PRICE_OBJ->kilo;
        $TBK_ORDER['PRICE_WITH_DISCOUNT'] = $price_result->priceWithDiscount;
        $TBK_ORDER['PRICE_WITHOUT_DISCOUNT'] = $price_result->priceWithoutDiscount;
        
        // VALIDAMOS lOS CAMPOS
        $this->TBK_ORDER = $TBK_ORDER;
        print_r($this->TBK_ORDER);
        $this->VALIDATE_FIELDS();

	}
	/* @method void validate_fields() This method create a preorder for pay with the process */
	function VALIDATE_FIELDS(){
		if($this->TBK_ORDER['WASH_TYPE'] < 0){
			echo "¡Washing is incorrect!";
			exit();
		}
		if(($this->PRICE_OBJ->WashType == WashType::DryCleaning || $this->PRICE_OBJ->WashType == WashType::SpecialCleaning)
	        && count($this->PRICE_OBJ->WashItemLines) < 1){
	        echo "Wash items should be selected for dry and special cleaning!";
			exit();
	    }    
		// Check request data
		if (empty($this->TBK_ORDER['NAME']))
		{
			echo "¡Ingrese su nombre!";
			exit();
		}
	    if(empty($this->TBK_ORDER['CITY_AREA_ID'])){
	        echo "¡Ingrese su ciudad!";
			exit();
	    }
	    if (empty($this->TBK_ORDER['ADDRESS']))
		{
			echo "¡Ingrese su dirección!";
			exit();
		}
	    if (!IsEmail($this->TBK_ORDER['EMAIL']))
		{
			echo "¡Email incorrecto!";
			exit();
		}
	    if(!$this->AUTORIZE){
	        echo "¡Debe aceptar los términos!";
			exit();
	    }
		if(empty($this->PRICE_OBJ->kilo) || $this->PRICE_OBJ->kilo > 1000)
		{
			echo "¡El peso debe ser entre 0 Kg y 1000 Kg!";
			exit();
		}
	    if(empty($this->TBK_ORDER['PICKUP_FROM'])){
	        echo "¡Elige cuándo pasamos a recoger tu ropa sucia!";
			exit();
	    }
	    if(empty($this->TBK_ORDER['DROPOFF_FROM'])){
	        echo "¡Elige cuándo quieres que te devolvamos tu ropa!";
			exit();
	    }
	}
	/* @method integer get_price() This method get the price generated by preorder */
	function GET_PRICE(){
		if($this->TBK_ORDER == NULL) throw new Exception("Preorder was not generated");
		return $this->TBK_ORDER['PRICE_WITH_DISCOUNT'];
	}
}
?>
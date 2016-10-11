<?php
include_once(dirname(__FILE__)."/_config.php");
//include_once(dirname(__FILE__)."/php/paypal/paypal.class.php");
include_once(dirname(__FILE__)."/php/MailService.class.php");
include_once(dirname(__FILE__)."/php/_helpers.php");
include_once(dirname(__FILE__)."/php/Price.class.php");
include_once(dirname(__FILE__)."/php/WashType.enum.php");
include_once(dirname(__FILE__)."/php/PickupTime.class.php");
include_once(dirname(__FILE__)."/php/kpf/flowAPI.php");
include_once(dirname(__FILE__)."/php/kpf/config.php");
include_once(dirname(__FILE__)."/php/OrderWashItemLine.class.php");
include_once(dirname(__FILE__)."/php/OrderCustomItemLine.class.php");



if($_POST) //Post Data received from order list page.
{
	$name =  GetPostNoLongerThan('name', 256); 
	$city_area_id = GetPostNoLongerThan('city_area_id',3);
    $address =  GetPostNoLongerThan('address', 1024); 
    $email = GetPostNoLongerThan('email', 124); 
    $phone = GetPostNoLongerThan('whatsapp', 20); 
    $discountCoupon = GetPostNoLongerThan('discount_coupon', 30);
    
    $pickuptimeSelected = GetPostNoLongerThan('pickup_datetime',50);
    $dropofftimeSelected = GetPostNoLongerThan('dropoff_datetime',50);
    
    $comment = GetPostNoLongerThan('comment', 3000); 
    
    
    $params = new PriceParameters();
    $params->kilo = 0;//default

    $washTypePost = GetPostNoLongerThan('laundry_option', 200);
    $params->WashType = WashType::ConvertFromPost($washTypePost);
    if ($params->WashType < 0)
	{
		echo "¡Washing is incorrect!";
		exit();
	}

    $checkboxWashing = GetBooleanPost('checkbox_washing');

    $ironingItemLines=[];
    if($params->WashType == WashType::WashingAndIroning){

        $ironing_items_post = isset($_POST['ironing_items_post']) ? $_POST['ironing_items_post']: "";
        $ironingItemLines =  OrderCustomItemLine::ConvertFromPost(WashType::OnlyIroning, $ironing_items_post);
        $params->TotalIroningItems = count($ironingItemLines);

        if($checkboxWashing){
            $orderWashitemLines = isset($_POST['washitems']) ? $_POST['washitems']: "";
            $params->WashItemLines = OrderWashItemLine::ConvertFromPost($params->WashType, $orderWashitemLines);
            $params->kilo = GetPost('weight');
        }
    }
    else if($params->WashType == WashType::OnlyIroning){
        $orderOnlyIroningItemLines = isset($_POST['only_ironing_items_post']) ? $_POST['only_ironing_items_post']: "";
        $params->WashItemLines = OrderWashItemLine::ConvertFromPost($params->WashType, $orderOnlyIroningItemLines);
        $params->kilo = GetPost('weight');
    }
    else if($params->WashType == WashType::DryCleaning){
        $orderDryCleaningItemLines = isset($_POST['dry_cleaning_items_post']) ? $_POST['dry_cleaning_items_post']: "";
        $params->WashItemLines = OrderWashItemLine::ConvertFromPost($params->WashType, $orderDryCleaningItemLines);
        $params->kilo = GetPost('weight');
    }
    
    

    if(($params->WashType == WashType::OnlyIroning ||
        $params->WashType == WashType::DryCleaning || 
        $params->WashType == WashType::SpecialCleaning)
        && count($params->WashItemLines) < 1){
        echo "Wash items should be selected for only ironing, dry and special cleaning!";
		exit();
    }    
    $termsAccepted = GetBooleanPost('terms');
        
	// Check request data
	if (empty($name))
	{
		echo "¡Ingrese su nombre!";
		exit();
	}
    if(empty($city_area_id)){
        echo "¡Ingrese su ciudad!";
		exit();
    }
    if (empty($address))
	{
		echo "¡Ingrese su dirección!";
		exit();
	}
    if (!IsEmail($email))
	{
		echo "¡Email incorrecto!";
		exit();
	}
    if(!$termsAccepted){
        echo "¡Debe aceptar los términos!";
		exit();
    }


	if(empty($params->kilo) || $params->kilo > 1000)
	{
		echo "¡El peso debe ser entre 0 Kg y 1000 Kg!";
		exit();
	}
    if(empty($pickuptimeSelected)){
        echo "¡Elige cuándo pasamos a recoger tu ropa sucia!";
		exit();
    }
    if(empty($dropofftimeSelected)){
        echo "¡Elige cuándo quieres que te devolvamos tu ropa!";
		exit();
    }
    $pickupdate = null;
    $price_result = null;
    try {
        $pickupdate = PickupTime::CreatePickupTimeFromString($pickuptimeSelected,'d/m/Y H:i','|');
        $dropoffdate = PickupTime::CreatePickupTimeFromString($dropofftimeSelected,'d/m/Y H:i','|');

        $params->Discount = DiscountCoupon::GetDiscountByCoupon($discountCoupon, $email);

        $price = Price::DefaultPrice();
        $price_result = $price->CalculatePrice($params); 

        $mysqli = OpenMysqlConnection(); 

        // Write to the database requested data
        $query = "INSERT INTO `".$DBName."`.`orders`";
        $query .= "(`NAME`,`CITY_AREA_ID`,`ADDRESS`,`PHONE`,`EMAIL`, `WEIGHT`, `PRICE_WITH_DISCOUNT`,`PRICE_WITHOUT_DISCOUNT`, `DISCOUNT_COUPON`,`WASH_TYPE`,`PICKUP_FROM`,`PICKUP_TILL`,`DROPOFF_FROM`,`DROPOFF_TILL`,`COMMENT`)";
            $query .= "VALUES('".$mysqli->real_escape_string($name)."', '".$mysqli->real_escape_string($city_area_id)."', '".$mysqli->real_escape_string($address)."', '".$mysqli->real_escape_string($phone)."', '".$mysqli->real_escape_string($email)."', '".$mysqli->real_escape_string($params->kilo)."','".$mysqli->real_escape_string($price_result->priceWithDiscount)."', '".$mysqli->real_escape_string($price_result->priceWithoutDiscount)."', '".$mysqli->real_escape_string($discountCoupon)."', '".$mysqli->real_escape_string($params->WashType)."', '".$pickupdate->from->format("Y-m-d H:i:s")."', '".$pickupdate->to->format("Y-m-d H:i:s")."', '".$dropoffdate->from->format("Y-m-d H:i:s")."', '".$dropoffdate->to->format("Y-m-d H:i:s")."','".$mysqli->real_escape_string($comment)."')";
    } catch (Exception $e) {
        RedirectToErrorPage($orderNumber,"Internal error Checkout_Post");
        
    }
	if($mysqli->query($query))
	{
        try {
            $insert_id = $mysqli->insert_id;
            $orderNumber = $OrdersNumberStart + $insert_id;
            $mysqli->query("UPDATE `".$DBName."`.`orders` SET ORDER_NUMBER='".$orderNumber."' WHERE `ID`='".$insert_id."'");
            $mysqli->close();
            
            if(!empty($params->WashItemLines)) {
                OrderWashItemLine::AddInitialItemsToOrder($orderNumber, $params->WashItemLines);
            }

            if(!empty($ironingItemLines)){
                OrderCustomItemLine::SetCustomOrderItems($orderNumber, $ironingItemLines, false);
            }
  
           
            // SEND EMAIL
            $mailService = new MailService();
            $mailService->SendNotification($orderNumber);
            // END OF EMAIL SENDING
            
            // Redirect to success page
            RedirectToSuccessOrderPage($orderNumber, $pickupdate->asText());
            exit();
           
        } catch (Exception $e) {
                //echo $e->getMessage();
                //"Error of sending details to Webpay.";
                RedirectToErrorPage($orderNumber,"Internal error 005");
                exit();
        } 
	}
	else
	{
         RedirectToErrorPage(null,"Internal error 003");
		 exit();
	}
 }
?>

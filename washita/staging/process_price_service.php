<?php  
// Include confi.php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/Price.class.php");
require_once(dirname(__FILE__)."/php/WashType.enum.php");
require_once(dirname(__FILE__)."/php/OrderWashItemLine.class.php");
require_once(dirname(__FILE__)."/php/DiscountCoupon.class.php");

// TEST 

// header('Content-type: application/json');

// $userCoupon = GetPostNoLongerThan('discount_coupon', 30);
// $email = GetPostNoLongerThan('email', 124);

// //     $orderWashitemLines  = explode(";",$_POST['washitems']);
//  echo json_encode(DiscountCoupon::GetDiscountByCoupon($userCoupon,$email) );
//  exit();
 
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $params = new PriceParameters();
    $params->kilo = GetPost('kilo');
    
    $washTypePost = GetPostNoLongerThan('laundry_option', 200);
    $params->WashType = WashType::ConvertFromPost($washTypePost);

    if($params->WashType == WashType::WashingAndIroning){
        $orderWashitemLines  = !empty($_POST['washitems'])? explode(";",$_POST['washitems']):"";

        $params->WashItemLines = OrderWashItemLine::ConvertFromPost($orderWashitemLines);

        $params->TotalIroningItems = GetPost('total_ironing_items');
    }
    else if($params->WashType == WashType::DryCleaning){
        $orderDryCleaningItemLines = !empty($_POST['dry_cleaning_items'])? explode(";",$_POST['dry_cleaning_items']): "";
        $params->WashItemLines = OrderWashItemLine::ConvertFromPost($orderDryCleaningItemLines);
    }

    $userCoupon = GetPostNoLongerThan('discount_coupon', 30);
    $email = GetPostNoLongerThan('email', 124);

    $params->Discount = DiscountCoupon::GetDiscountByCoupon($userCoupon,$email);

    $price = Price::DefaultPrice();
    $result = $price->CalculatePrice($params);
    // Result to JSON
    $json = json_encode($result);
}

/* Output header */
 header('Content-type: application/json');
 echo json_encode($json);

?>
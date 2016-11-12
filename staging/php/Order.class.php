<?php 

include_once(dirname(__FILE__)."/../_config.php");
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/PickupTime.class.php");
include_once(dirname(__FILE__)."/CityAndArea.class.php");
include_once(dirname(__FILE__)."/WashType.enum.php");
include_once(dirname(__FILE__)."/DiscountCoupon.class.php");
include_once(dirname(__FILE__)."/DiscountCouponType.enum.php");
require_once(dirname(__FILE__)."/hybridauth/WashitaUser.php");
require_once(dirname(__FILE__)."/hybridauth/UserType.enum.php");


class Order{

    /** @var int */
    var $Id;
    
    /** @var string */
    var $OrderNumber;

    /** @var string */
    var $Name;
  
    /** @var int */
    var $CityAreaId;

    private $cityAndArea;
  
    /** @var string */
    var $Address;
    
    /** @var string */
    var $Email;
    
    /** @var string */
    var $Phone;
    
    /** @var decimal */
    var $Weight;
    
    /** @var decimal */
    var $ActualWeight;
    
    /** @var string */
    var $DiscountCoupon;
  
    /** @var PickupTime */
    var $PickupTime;
    /** @var PickupTime */
    var $DropOff;
    
    /** @var decimal */
    var $PriceWithoutDiscount;
    /** @var decimal */
    var $PriceWithDiscount;
    
    /** @var decimal */
    var $AdditionalPriceWithoutDiscount;
    /** @var decimal */
    var $AdditionalPriceWithDiscount;

    var $ActualPriceWithDiscount;
    
    /** @var string */
    var $Comment;
    
    
    /** @var int */
    var $PaymentStatus;

    /** @var int */
    var $WashType;

    var $WashDetergent;

    
    public static function GetOrderByNumber($orderNumber){
        global $DBName;
         
        if(!isset($orderNumber) || !$orderNumber){
            return null;
        }
         
        $order = null;
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT ID, ORDER_NUMBER, NAME, CITY_AREA_ID, ADDRESS, EMAIL, PHONE, 
            WEIGHT,DISCOUNT_COUPON,PRICE_WITH_DISCOUNT,PRICE_WITHOUT_DISCOUNT,
            ACTUAL_WEIGHT, ADDITIONAL_PRICE_WITHOUT_DISCOUNT, ADDITIONAL_PRICE_WITH_DISCOUNT,
            PAYMENT_STATUS, WASH_TYPE,
            PICKUP_FROM, PICKUP_TILL,
            DROPOFF_FROM, DROPOFF_TILL, COMMENT, ACTUAL_PRICE_WITH_DISCOUNT, 
            WASH_DETERGENT
            FROM `".$DBName."`.`orders` WHERE ORDER_NUMBER = '".$mysqli->real_escape_string($orderNumber)."'";
            
            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                
                $order = self::createOrderFromRow($row);
                
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $order;
    }
    
    public static function GetOrderNumberByFeedbackCode($feedbackCode){
        global $DBName;
         
        if(!$feedbackCode){
            return null;
        }
         
        $order = null;
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT ID,ORDER_NUMBER, NAME, CITY_AREA_ID, ADDRESS, EMAIL, PHONE, 
            WEIGHT,DISCOUNT_COUPON,PRICE_WITH_DISCOUNT,PRICE_WITHOUT_DISCOUNT,
            ACTUAL_WEIGHT, ADDITIONAL_PRICE_WITHOUT_DISCOUNT, ADDITIONAL_PRICE_WITH_DISCOUNT,
            PAYMENT_STATUS, WASH_TYPE,
            PICKUP_FROM, PICKUP_TILL,
            DROPOFF_FROM, DROPOFF_TILL, COMMENT, ACTUAL_PRICE_WITH_DISCOUNT, 
            WASH_DETERGENT
            FROM `".$DBName."`.`orders` WHERE FEEDBACK_CODE = '".$mysqli->real_escape_string($feedbackCode)."'";
            
            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                
                $order = self::createOrderFromRow($row);

                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $order;
    }
    
     private static function createOrderFromRow($row){
         if(!$row["ID"]){
             return null;
         }
        $order = new Order();
                
        $order->Id = $row["ID"];
        $order->OrderNumber = $row["ORDER_NUMBER"];
        $order->Name = $row["NAME"];
        $order->CityAreaId = $row["CITY_AREA_ID"];
        $order->Address = $row["ADDRESS"];
        $order->Email = $row["EMAIL"];
        $order->Phone = $row["PHONE"];
        
        $order->Weight = $row['WEIGHT']; 
        $order->DiscountCoupon = $row['DISCOUNT_COUPON']; 
        $order->PriceWithDiscount = $row['PRICE_WITH_DISCOUNT'];
        $order->PriceWithoutDiscount = $row['PRICE_WITHOUT_DISCOUNT'];
        
        $order->ActualWeight = $row['ACTUAL_WEIGHT'];
        $order->AdditionalPriceWithoutDiscount = $row['ADDITIONAL_PRICE_WITHOUT_DISCOUNT'];
        $order->AdditionalPriceWithDiscount = $row['ADDITIONAL_PRICE_WITH_DISCOUNT'];

        $order->ActualPriceWithDiscount = $row['ACTUAL_PRICE_WITH_DISCOUNT'];

        
        
        $order->PaymentStatus = $row['PAYMENT_STATUS'];
        $order->WashType = $row['WASH_TYPE'];
        
        $order->WashDetergent = $row['WASH_DETERGENT'];

        $order->Comment = $row['COMMENT'];
        
        $pickupFrom = CreateDateTimeImmutableFromMutable(new DateTime($row["PICKUP_FROM"]));
        $pickupTill = CreateDateTimeImmutableFromMutable(new DateTime($row["PICKUP_TILL"]));
        $order->PickupTime = PickupTime::CreatePickupTime($pickupFrom,$pickupTill);
        
        $dropoffFrom = CreateDateTimeImmutableFromMutable(new DateTime($row["DROPOFF_FROM"]));
        $dropoffTill = CreateDateTimeImmutableFromMutable(new DateTime($row["DROPOFF_TILL"]));
        
        $order->DropOff  = PickupTime::CreatePickupTime($dropoffFrom,$dropoffTill);
        
        return $order;
    }
    
    public static function SetFeedbackIsRequested($orderNumber){
        global $DBName;
        try{
            $mysqli = OpenMysqlConnection(); 
            // Write to the database requested data
            $query = "UPDATE `".$DBName."`.`orders`
                    SET IS_FEEDBACK_REQUESTED=1
                    WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'";

            $mysqli->query($query);
            if($mysqli){
                $mysqli->close();
            }   
        }
        catch(Exception $e) {
                
        }
    }

    public static function SetOrderPaymentStatus($orderNumber, $paymentStatus){
        global $DBName;
        try{
            $mysqli = OpenMysqlConnection(); 
            // Write to the database requested data
            $query = "UPDATE `".$DBName."`.`orders`
                    SET PAYMENT_STATUS='".$paymentStatus."' 
                    WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'";

            $mysqli->query($query);
            if($mysqli){
                $mysqli->close();
            }   
        }
        catch(Exception $e) {
                
        }
    }

    public function UseCoupon(){
        global $DiscountInfluencerValue;
        $discount = DiscountCoupon::GetDiscountByCoupon($this->DiscountCoupon, $this->Email, $this->OrderNumber);

        if($discount != null &&
            $discount->IsValid() &&
            $discount->Value != null)
        {
            if($discount->CouponType == DiscountCouponType::StarterKitByInfluencer){
                $result = WashitaUser::AddToPersonalDiscount($discount->InfluencerId, $DiscountInfluencerValue);
            }
            else if($discount->CouponType == DiscountCouponType::UserPersonal){
                $discountTaken = 0;
                if($this->AdditionalPriceWithDiscount != null && $this->AdditionalPriceWithDiscount >0){
                    $discountTaken = $this->AdditionalPriceWithoutDiscount - $this->AdditionalPriceWithDiscount;
                }
                else{
                    $discountTaken = $this->PriceWithoutDiscount - $this->PriceWithDiscount;
                }

                if($discountTaken > 0){
                    WashitaUser::SubtractFromPersonalDiscount($discountTaken);
                }
            }

            DiscountCoupon::UseDiscount($discount);
        }
    }

    public function TotalDiscount(){

    }
    
    public function HasWashing(){
        return $this->WashType != WashType::OnlyIroning;
    }

    public function IsWeightRequired(){
        return $this->WashType == WashType::WashingAndIroning
            || $this->WashType == WashType::OnlyIroning;
    }

    public function WashingTypeText(){
        return WashType::ToString($this->WashType);
    }


    public function GetCityAndArea(){
        
        if(!isset($this->cityAndArea) && isset($this->CityAreaId)){
            $this->cityAndArea = CityAndArea::GetCityAndAreaByCityAreaId($this->CityAreaId);
        }

        return $this->cityAndArea;
    }

    public function GetFullAddress(){
        $cityAndArea = $this->GetCityAndArea();
        return $cityAndArea->GetFullName().", ".$this->Address;
    }
    
    public function __toString()
    {
        return "Order";
    }
}


?>
<?php
require_once(dirname(__FILE__)."/_helpers.php");
require_once(dirname(__FILE__)."/DiscountCouponValue.class.php");
require_once(dirname(__FILE__)."/DiscountCouponUsage.class.php");
require_once(dirname(__FILE__)."/DiscountCouponType.enum.php");


require_once(dirname(__FILE__)."/CurrentDateTime.class.php");
require_once(dirname(__FILE__)."/hybridauth/WashitaHybridAuth.php");
require_once(dirname(__FILE__)."/hybridauth/WashitaUser.php");
require_once(dirname(__FILE__)."/hybridauth/UserType.enum.php");


class DiscountCoupon{
    /**
     * @var string
     */
    public $Coupon;
    /**
     * @var DiscountCouponValue
     */
    public $Value;

    public $WarningMessage;

    public $InfluencerId;

    /**
     * @var DiscountCouponType
     */
    public $CouponType;

    /**
     * @var DiscountCouponUsage
     */
    public $Usage;  


    private $isValidByTime;

    function __construct($isValidByTime = true, DiscountCouponUsage $usage = null){
        $this->isValidByTime = $isValidByTime;
        $this->Usage = $usage;
    }

    public function IsValid(){
        return $this->IsValidByTime() && $this->IsValidByUsage();
    }

    public function IsValidByTime(){
        return $this->isValidByTime;
    }

    public function IsValidByUsage(){

        return $this->Usage == null || $this->Usage->IsValid();
    }

    public static function GetDiscountByCoupon($coupon, $email, $ignoreOrderNumberUsage=""){
        global $DiscountPersonalCode;
        if($coupon == $DiscountPersonalCode){
            return self::CurrentPersonalDiscount();
        }

        $discount = self::getDiscountObjectByCoupon($coupon);
        if($discount != null){
            if(!$discount->IsValidByTime()){
                $discount->WarningMessage = "Este código ha caducado"; 
                return $discount;
            }
            elseif(!$discount->IsValidByUsage()){
                $discount->WarningMessage = "Este código ya fue utilizado";
                return $discount;
            } 
            elseif($discount->CouponType == DiscountCouponType::StarterKitByInfluencer) {              
                $user = WashitaUser::CurrentSessionUser();
                if($user != null && $user->UserType == UserType::Influencer){
                    $discount->Value = null;
                    $discount->WarningMessage = "El código de invitación no puede ser usado por ti mismo";
                    return $discount;
                }
                elseif(self::isCouponUsedByUser($email, $coupon, $ignoreOrderNumberUsage)){
                    $discount->Value = null;
                    $discount->WarningMessage = "Ya haz utilizado un código de invitación para primer pedido";
                    return $discount;
                }
            }
            elseif($discount->CouponType = DiscountCouponType::OneTimePerEmail && 
                    self::isCouponUsedByUser($email, $coupon, $ignoreOrderNumberUsage)){
                $discount->Value = null;
                $discount->WarningMessage = "Ya haz utilizado un código de invitación para primer pedido";
                return $discount;
            }
        }
        
        return $discount;
    }

    public static function CurrentPersonalDiscount(){
        global $DiscountPersonalCode;
        $user = WashitaUser::CurrentUser();
        if($user != null && $user->UserType == UserType::Influencer){

            $discount = new DiscountCoupon();
            $discount->Coupon = $DiscountPersonalCode;
            $discount->InfluencerId = null;
            $discount->CouponType = DiscountCouponType::UserPersonal;

            $discountValue = new DiscountCouponValue();
            $discountValue->Value = $user->PersonalDiscountAmount;
            $discountValue->IsPercent = false;
            $discount->Value = $discountValue;

            return $discount;
        }

        return null;
    }

    private static function getDiscountObjectByCoupon($coupon){
        global $DBName, $DiscountCouponLengthMin;
        if(empty($coupon) || strlen($coupon) < $DiscountCouponLengthMin){
            return null;
        }

        $result = null;
        try{
            $mysqli = OpenMysqlConnection(); 
             $query = "SELECT ID, COUPON, VALUE, (VALID_TILL > NOW()) as IS_VALID_BY_TIME, 
                              IS_PERCENT, INFLUENCER_USER_ID, USED, MAX_USAGE, IS_ONE_TIME_PER_EMAIL
                  FROM `".$DBName."`.`discount` WHERE `COUPON`='".$mysqli->real_escape_string($coupon)."' LIMIT 1";

            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                $result = self::createDiscountFromRow($row);
                $sql_result->free();
            }
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    }

     private static function isCouponUsedByUser($email, $coupon, $ignoreOrderNumberUsage){
        global $DBName;
        if(empty($email)){
            return false;
        }

        $result = false;
        try{
            $mysqli = OpenMysqlConnection(); 
             $query = "SELECT orders.ID 
                        FROM `".$DBName."`.`orders`, `".$DBName."`.`discount` 
                        WHERE orders.EMAIL ='".$mysqli->real_escape_string($email)."'
                              AND  orders.PAYMENT_STATUS = 2
                              AND orders.ORDER_NUMBER <> '".$ignoreOrderNumberUsage."'
                              AND orders.DISCOUNT_COUPON = '".$coupon."'
                              AND orders.DISCOUNT_COUPON = discount.COUPON
                        LIMIT 1";

            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                if(isset($row["ID"]) && $row["ID"] > 0){
                   $result = true;
                }
                $sql_result->free();
            }
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    } 

   

     private static function createDiscountFromRow($row){
         if(!$row["ID"]){
             return null;
         }
        
        $discountCouponUsage =  new DiscountCouponUsage($row["USED"],$row["MAX_USAGE"]);
        $discount = new DiscountCoupon($row["IS_VALID_BY_TIME"], $discountCouponUsage);
        $discount->Coupon = $row["COUPON"];
        $discount->InfluencerId = $row["INFLUENCER_USER_ID"];

        if($discount->IsValid()){
            $discountValue = new DiscountCouponValue();
            $discountValue->Value = $row["VALUE"];
            $discountValue->IsPercent = $row["IS_PERCENT"];
            $discount->Value = $discountValue;
        }
        else{
            $discount->Value = null;
        }

        if(!empty($row["INFLUENCER_USER_ID"])){
            $discount->CouponType = DiscountCouponType::StarterKitByInfluencer;
        }
        else if($row["IS_ONE_TIME_PER_EMAIL"]){
            $discount->CouponType = DiscountCouponType::OneTimePerEmail;
        }
        else{
            $discount->CouponType = DiscountCouponType::Normal;
        }

        return $discount;
     }    



    public static function CreateDiscountForInfluencer($userId, $registrationCode){
        global $DBName, $DiscountInfluencerValue;

        $result = new Result();
        $result->success = false;

        $discount = self::GetDiscountByCoupon($registrationCode,'');
        if($discount != null){
            $result->message = "Error, discount ".$registrationCode." already exists";
        }
        else{
            try{
            $mysqli = OpenMysqlConnection(); 

            $validTill = CurrentDateTime::Now();
            $validTill->modify('+1 year');

            global $DiscountInfluencerMaxUsageForFriends;
            // Write to the database requested data
            $query = "INSERT INTO `".$DBName."`.`discount`(COUPON, VALUE, IS_PERCENT, VALID_TILL, INFLUENCER_USER_ID,
                                                        MAX_USAGE, 
                                                        USED, 
                                                        IS_ONE_TIME_PER_EMAIL)
                    values('".$mysqli->real_escape_string($registrationCode)."'
                          ,'".$mysqli->real_escape_string($DiscountInfluencerValue)."'
                          , 0
                          ,'".$validTill->format("Y-m-d")."'
                          ,'".$mysqli->real_escape_string($userId)."'
                          , ".$DiscountInfluencerMaxUsageForFriends."
                          , 0
                          , 0
                    )";

                if($mysqli->query($query)){
                    $result->success = true;  
                }
                if($mysqli){
                    $mysqli->close();
                }   
            }
            catch(Exception $e) {
                    
            }

        }    
        
        if(!$result->success && empty($result->message)){
            $result->message = "Internal error creating discount for influencer user type";
        }
        else{
            //$result->value = self::GetDiscountByCoupon($registrationCode,'');      
        }

        return $result;
    }



    public static function UseDiscount(DiscountCoupon $discount){
        global $DBName;
        
        if($discount->Usage != null){
            $discount->Usage->IncreaseCurrentCount();

            try{
                $mysqli = OpenMysqlConnection(); 
                // Write to the database requested data
                $query = "UPDATE `".$DBName."`.`discount`
                        SET USED = USED + 1
                        WHERE COUPON='".$mysqli->real_escape_string($discount->Coupon)."'";

                $mysqli->query($query);
                if($mysqli){
                    $mysqli->close();
                }   
            }
            catch(Exception $e) {
                    
            }
        }
        
    }


    public function __toString() {
        return "DiscountCoupon";
    }
}

?>
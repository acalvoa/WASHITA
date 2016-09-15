<?php
require_once(dirname(__FILE__)."/_helpers.php");

class DiscountCouponValue{
    /**
    * @var decimal
    */
    public $Value;
    
    public $IsPercent;
    
    public function __toString() {
        return "DiscountCouponValue";
    }

    public function ToString(){
        return $this->IsPercent
                ? round($this->Value,2)."%"
                : MoneyFormat(ceil($this->Value));
    }

    public function GetPriceAfterDiscountFor($price){
        if($this->IsPercent){
            return  $price * ((100 - $this->Value)/100);
        }

        // Non procent        
        $substractedValue = $price - $this->Value;
        return $substractedValue < 0? 0: $substractedValue;
    }
}

?>
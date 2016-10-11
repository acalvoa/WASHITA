<?php
include_once(dirname(__FILE__)."/../_config.php");
include_once(dirname(__FILE__)."/WashType.enum.php");
require_once(dirname(__FILE__)."/DiscountCoupon.class.php");

class PriceParameters{
    var $kilo;
    public $Discount;
    public $WashType;
    public $WashItemLines;
    public $TotalIroningItems;
    
    public function __construct(){
        $this->kilo = 0;
    }
}

class PriceResult {
    var $priceWithoutDiscount;
    var $priceWithoutDiscountText;
    var $priceWithDiscount;
    var $priceWithDiscountText;
    var $WashType;
    var $washTypeText;
    var $discountValueText;
    var $discountWarningMessage;
    var $pricePerOneKilo;
    var $pricePerOneKiloText;
    var $pricePerOneItemIroningText;
    var $totalIroningItems;
    var $weight;
}

class Price {
    var $pricePerOneKilo;
    var $pricePerKiloStartingFiveKiloPack;
    var $priceForIroningPerKilo;
    var $priceForIroningPerItem;

    private function __construct(){
        
    }
    
    public static function DefaultPrice(){
        $obj = new Price();
        $obj->pricePerOneKilo = $GLOBALS['PricePerOneKilo'];
        $obj->pricePerKiloStartingFiveKiloPack = $GLOBALS['PricePerKiloStartingFiveKiloPack'];
        $obj->priceForIroningPerKilo = $GLOBALS['PriceForIroningPerKilo'];
        $obj->priceForIroningPerItem = $GLOBALS['PriceForIroningPerItem'];
        
        return $obj;
    }
    
    public static function PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo, $priceForIroningPerItem){
        $obj = new Price();
        $obj->pricePerOneKilo = $pricePerOneKilo;
        $obj->pricePerKiloStartingFiveKiloPack = $pricePerKiloStartingFiveKiloPack;
        $obj->priceForIroningPerKilo = $priceForIroningPerKilo;
        $obj->priceForIroningPerItem = $priceForIroningPerItem;
       
        return $obj;
    }
    
    // Price calculation
    public function CalculatePrice(PriceParameters $params) {
        $result = new PriceResult();
        
        $result->weight = $params->kilo;
        $result->WashType = $params->WashType;
        $result->washTypeText = WashType::ToString($params->WashType);

        $result->priceForIroningPerItemText = MoneyFormat($this->priceForIroningPerItem);
        $result->totalIroningItems = $params->TotalIroningItems;

        //WashItemLines
        if($params->WashType == WashType::WashingAndIroning){
            $result->pricePerOneKilo  = $params->kilo < 6? $this->pricePerOneKilo: $this->pricePerKiloStartingFiveKiloPack;                       
            $result->priceWithoutDiscount = ($result->pricePerOneKilo * $params->kilo);

            // ironing
            $result->priceWithoutDiscount += ($this->priceForIroningPerItem * $params->TotalIroningItems);
        }
        else if($params->WashType == WashType::DryCleaning){
            $result->priceWithoutDiscount = 0;
            if(!empty($params->WashItemLines)){
                foreach ($params->WashItemLines as $washItemLine) {
                    $result->priceWithoutDiscount += ($washItemLine->Count * $washItemLine->WashItem->DryCleanPrice);
                }
            }
        }
        else if($params->WashType == WashType::SpecialCleaning){
            $result->priceWithoutDiscount = 0;
            if(!empty($params->WashItemLines)){
                foreach ($params->WashItemLines as $washItemLine) {
                    $result->priceWithoutDiscount += ($washItemLine->Count * $washItemLine->WashItem->SpecialCleanPrice);
                }
            }
        }
        else{
            // 
        }


        $result->priceWithDiscount = $result->priceWithoutDiscount; //default
        $result->discountValueText = "0%"; //default
        if($params->Discount != null){
            $result->discountWarningMessage = $params->Discount->WarningMessage;
            if($params->Discount->IsValid() && $params->Discount->Value != null){
                $result->discountValueText = $params->Discount->Value->ToString();
                
                $result->priceWithDiscount = $params->Discount->Value->GetPriceAfterDiscountFor($result->priceWithoutDiscount);
            }
        }

        // FLOW service does not allow payment less than 350
        if($result->priceWithDiscount >0 && $result->priceWithDiscount < 350){
            $result->priceWithDiscount = 350;
        }

        $result->priceWithDiscount = ceil($result->priceWithDiscount);//rounding
        $result->priceWithDiscountText = MoneyFormat($result->priceWithDiscount);
        $result->priceWithoutDiscountText = MoneyFormat($result->priceWithoutDiscount);
        $result->pricePerOneKiloText = MoneyFormat($result->pricePerOneKilo);

        return $result;
    }
}


?>
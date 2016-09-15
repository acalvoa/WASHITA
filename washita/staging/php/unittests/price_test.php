<?php
require_once(dirname(__FILE__)."/simpletest/autorun.php");
require_once(dirname(__FILE__).'/../Price.class.php');
require_once(dirname(__FILE__).'/../DiscountCoupon.class.php');
require_once(dirname(__FILE__).'/../OrderWashItemLine.class.php');
require_once(dirname(__FILE__).'/../WashItem.class.php');


class TestOfPrice extends UnitTestCase {
    function testTwoKiloPrice() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 2;
        $params->Discount = null;
        
        $result = $price->CalculatePrice($params);
         
        $this->assertEqual($result->weight,2);
        $this->assertEqual($result->pricePerOneKilo,2600);
        $this->assertEqual($result->priceWithoutDiscount,5200);
        $this->assertEqual($result->priceWithoutDiscount,$result->priceWithDiscount);
    }
    
    function testFiffteenKiloPrice() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 15;
        $params->Discount = null;
        
        $result = $price->CalculatePrice($params);

        $this->assertEqual($result->weight,15);
        $this->assertEqual($result->pricePerOneKilo,2200);
        $this->assertEqual($result->priceWithoutDiscount,33000);
        $this->assertEqual($result->priceWithoutDiscount,$result->priceWithDiscount);
    }
    
    function testThirteenKiloPrice() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 13;
        $params->ironing = false;
        $params->Discount = null;
        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->priceWithoutDiscount,28600);
        $this->assertEqual($result->pricePerOneKilo,2200);
        
        $this->assertEqual($result->priceWithoutDiscount,$result->priceWithDiscount);
    }
    
 
    function testOneKiloAndPercentDiscountPrice() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 1;

        $params->Discount = new DiscountCoupon();
        $discountValue = new DiscountCouponValue();
        $discountValue->Value = 20;
        $discountValue->IsPercent = true;
        $params->Discount->Value = $discountValue;

        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->priceWithoutDiscount,2600);
        
        $this->assertEqual($result->priceWithDiscount, 2080);
    }

    function testOneKiloAndHugeSubstractionDiscountPrice() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 1;

        $params->Discount = new DiscountCoupon();
        $discountValue = new DiscountCouponValue();
        $discountValue->Value = 30000;
        $discountValue->IsPercent = false;
        $params->Discount->Value = $discountValue;

        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->priceWithoutDiscount,2600);
        
        $this->assertEqual($result->priceWithDiscount, 0);
    }
    
    function testTwoKilosOnlyIroning() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 2;
        $params->WashType = 1;
        
        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->weight,2);
        $this->assertEqual($result->pricePerOneKilo,1500);
        $this->assertEqual($result->priceWithoutDiscount,3000);
        $this->assertEqual($result->priceWithoutDiscount,$result->priceWithDiscount);
    }
    
    function testThreeKilosOnlyIroningWithSubstractionDiscount() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 3;
        $params->WashType = 1;
        
        $params->Discount = new DiscountCoupon();
        $discountValue = new DiscountCouponValue();
        $discountValue->Value = 1000;
        $discountValue->IsPercent = false;
        $params->Discount->Value = $discountValue;
        
        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->weight,3);
        $this->assertEqual($result->pricePerOneKilo,1500);
        $this->assertEqual($result->priceWithoutDiscount,4500);
        $this->assertEqual($result->priceWithDiscount,3500);
    }


    function testDryCleaningAndSubstractionDiscount() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 3000; //should not affect price as calculated by items
        $params->WashType = 2;

         $orderWashItemLines = [];

         $line = new OrderWashItemLine();
         $line->Count = 2;
         $washItem = new WashItem();
         $washItem->DryCleanPrice = 1000;
         $washItem->Weight = 3000; //should not affect price
         $washItem->SpecialCleanPrice = 3000; //should not affect price
         $line->WashItem = $washItem;
         $orderWashItemLines[] = $line;

         $line2 = new OrderWashItemLine();
         $line2->Count = 2;
         $washItem2 = new WashItem();
         $washItem2->Weight = 3000; //should not affect price
         $washItem2->DryCleanPrice = 1500;
         $washItem2->SpecialCleanPrice = 3000; //should not affect price
         $line2->WashItem = $washItem2;
         $orderWashItemLines[] = $line2;
         
        $params->WashItemLines = $orderWashItemLines;
         
        $params->Discount = new DiscountCoupon();
        $discountValue = new DiscountCouponValue();
        $discountValue->Value = 500;
        $discountValue->IsPercent = false;
        $params->Discount->Value = $discountValue;
        
        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->priceWithoutDiscount,5000);
        $this->assertEqual($result->priceWithDiscount,4500);
    }


    function testSpecialCleaningAndPercentDiscount() {
        // $price = Price::PriceFromParameters($pricePerOneKilo, $pricePerKiloStartingFiveKiloPack, $priceForIroningPerKilo)
        $price = Price::PriceFromParameters(2600, 2200, 1500);
        
        $params = new PriceParameters();
        $params->kilo = 3000; //should not affect price as calculated by items
        $params->WashType = 3;

         $orderWashItemLines = [];

         $line = new OrderWashItemLine();
         $line->Count = 2;
         $washItem = new WashItem();
         $washItem->DryCleanPrice = 3000; //should not affect price
         $washItem->Weight = 3000; //should not affect price
         $washItem->SpecialCleanPrice = 1000;
         $line->WashItem = $washItem;
         $orderWashItemLines[] = $line;

         $line2 = new OrderWashItemLine();
         $line2->Count = 2;
         $washItem2 = new WashItem();
         $washItem2->Weight = 3000; //should not affect price
         $washItem2->DryCleanPrice = 3000; //should not affect price
         $washItem2->SpecialCleanPrice = 1500; 
         $line2->WashItem = $washItem2;
         $orderWashItemLines[] = $line2;
         
        $params->WashItemLines = $orderWashItemLines;
         
        $params->Discount = new DiscountCoupon;
        $discountValue = new DiscountCouponValue();
        $discountValue->Value = 20;
        $discountValue->IsPercent = true;
        $params->Discount->Value = $discountValue;
        
        $result = $price->CalculatePrice($params);
        
        $this->assertEqual($result->priceWithoutDiscount,5000);
        $this->assertEqual($result->priceWithDiscount,4000);
    }

}
?>
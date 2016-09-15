<?php

class DiscountCouponUsage{
    private $currentCount;
    
    private $maxCount;

    function __construct($currentCount, $maxCount){
        $this->currentCount = $currentCount;
        $this->maxCount = $maxCount;
    }

    public function IsValid(){
        return $this->currentCount < $this->maxCount;
    }

    public function IncreaseCurrentCount(){
        $this->currentCount = $this->currentCount + 1;
    }
}

?>
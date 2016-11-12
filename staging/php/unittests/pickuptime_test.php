<?php
require_once(dirname(__FILE__)."/simpletest/autorun.php");
require_once(dirname(__FILE__).'/../PickupTime.class.php');
require_once(dirname(__FILE__).'/../CurrentDateTime.class.php');


class TestOfPickupTime extends UnitTestCase {

    
    function testGetPickupTimesForDuration(){
        $fromDateTime = DateTimeImmutable::createFromFormat("d-M-Y",'01-Sep-2016');
        $toDateTime = DateTimeImmutable::createFromFormat("d-M-Y H:i",'02-Sep-2016 23:59');
          
        $dates = PickupTime::GetPickupTimesForDuration($fromDateTime,$toDateTime);
        
        $this->assertIdentical(count($dates),4);
        
    }

    function testGetPickupTimesForDurationOnWeekends(){
        $fromDateTime = DateTimeImmutable::createFromFormat("d-M-Y",'02-Sep-2016');
        $toDateTime = DateTimeImmutable::createFromFormat("d-M-Y H:i",'05-Sep-2016 23:59');
          
        $dates = PickupTime::GetPickupTimesForDuration($fromDateTime,$toDateTime);
        
        $this->assertIdentical(count($dates),4);
        
    }
    
    function testMinPickupTime_EarlyMorningFriday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '02-Sep-2016 07:50'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "02-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "02-Sep-2016 10:00");
    }
    
    function testMinPickupTime_LateMorningFriday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '02-Sep-2016 10:30'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "02-Sep-2016 16:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "02-Sep-2016 18:00");
    }
    
    function testMinPickupTime_LateEveningMonday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '05-Sep-2016 21:20'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        //next morning
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "06-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "06-Sep-2016 10:00");
    }
    
    function testMinPickupTime_LateEveningMonday_2() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '05-Sep-2016 18:01'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        //next morning
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "06-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "06-Sep-2016 10:00");
    }
    
    function testMinPickupTime_EarlyEveningFriday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '02-Sep-2016 15:30'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        //next morning
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "02-Sep-2016 16:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "02-Sep-2016 18:00");
    }
    
    
    function testEveningPickupTimeFriday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '02-Sep-2016 21:20'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "05-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "05-Sep-2016 10:00");
    }

    function testMorningPickupTimeSaturday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '03-Sep-2016 06:20'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "05-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "05-Sep-2016 10:00");
    }




    function testMorningPickupTimeSunday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '04-Sep-2016 06:20'));
        
        $dateRange = PickupTime::GetMinPickupTime();
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "05-Sep-2016 08:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "05-Sep-2016 10:00");
    }
    
    function testPreviousEveningPickupTimeMonday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '05-Sep-2016 07:00'));
        $pickuptime = PickupTime::GetMinPickupTime();
        $dateRange = $pickuptime->PreviousNearestPoint();
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "02-Sep-2016 16:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "02-Sep-2016 18:00");
    }
    
    function testPreviousMorningPickupTimeFriday() {
        CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '02-Sep-2016 00:00'));
        $pickuptime = PickupTime::GetMinPickupTime();
        $dateRange = $pickuptime->PreviousNearestPoint();
        $this->assertIdentical($dateRange->from->format("d-M-Y H:i"), "01-Sep-2016 16:00");
        $this->assertIdentical($dateRange->to->format("d-M-Y H:i"), "01-Sep-2016 18:00");
    }
}
?>
<?php 

include_once(dirname(__FILE__)."/../_config.php");
include_once(dirname(__FILE__)."/CurrentDateTime.class.php");
require_once(dirname(__FILE__)."/NonworkingDays.class.php");
include_once(dirname(__FILE__)."/_helpers.php");

class PickupTime{
    /** @var DateTimeImmutable */
    var $from;
    /** @var DateTimeImmutable */
    var $to;
    
    private function __construct(){
        
    }

    static function isWeekend(DateTimeImmutable $dateImmutable) {
        return $dateImmutable->format('N') >= 6;    
    }

    static function isNonWorkingDate(DateTimeImmutable $dateImmutable) {
        $result = false;
        $nonWorkingDates = NonworkingDays::GetNonWokingDaysForFuturePeriod(93/*nearest days*/);
        foreach($nonWorkingDates as $nonWorkingDate){
            if($dateImmutable ==  $nonWorkingDate){
                $result = true;
                break; 
            }
        }

        return $result;
    }

    
    
    public static function CreateMorningPickup(DateTimeImmutable $dateImmutable){
        $fromMorning = $dateImmutable->setTime(8, 0);
        $tillMorning = $dateImmutable->setTime(10, 0);
        return PickupTime::CreatePickupTime($fromMorning,$tillMorning);
    }
    
    public static function CreateEveningPickup(DateTimeImmutable $dateImmutable){
        $fromEvening = $dateImmutable->setTime(16, 0);
        $tillEvening = $dateImmutable->setTime(18, 0);
        return PickupTime::CreatePickupTime($fromEvening,$tillEvening);
    }
    
    public static function TodayMorning(){
        $currentDatetime = CreateDateTimeImmutableFromMutable(CurrentDateTime::Now());
        return PickupTime::CreateMorningPickup($currentDatetime);
    }
    
    public static function TodayEvening(){
        $currentDatetime = CreateDateTimeImmutableFromMutable(CurrentDateTime::Now());
        return PickupTime::CreateEveningPickup($currentDatetime);
    }
    
    public function IsMorning(){
        return $this->from->format("H:i")=="08:00";
    }
    public function IsEvening(){
        return $this->from->format("H:i")=="16:00";
    }
    //Previous morning or evening
    public function PreviousNearestPoint(){
        if($this->IsEvening()){
            return self::CreateMorningPickup($this->from);
        }
        else
        {
            $previousWorkingDay = $this->from->modify("-1 days");
            while(self::isWeekend($previousWorkingDay) ||
                 self::isNonWorkingDate($previousWorkingDay)){
                $previousWorkingDay = $previousWorkingDay->modify("-1 days");
            }
            return self::CreateEveningPickup($previousWorkingDay);
        }
    }

    //Next morning or evening
    public function NextNearestPoint(){
        if($this->IsMorning()){
            return self::CreateEveningPickup($this->from);
        }
        else
        {
            $nextWorkingDay = $this->from->modify("+1 days");
            while(self::isWeekend($nextWorkingDay)||
                 self::isNonWorkingDate($nextWorkingDay)){
                $nextWorkingDay = $nextWorkingDay->modify("+1 days");
            }
            return self::CreateMorningPickup($nextWorkingDay);
        }
    }
    public function NextPickup(){
        $nextPickup;
        //1.5 day
        for($i=0; $i<3;$i++){
            $nextPickup =$this->NextNearestPoint();
        }
        return $nextPickup;        
    }  
    
    public static function CreatePickupTime(DateTimeImmutable $from, DateTimeImmutable $to){
        $obj = new PickupTime();
        $obj->from = $from;
        $obj->to = $to;
        
        return $obj;
    }
     public static function CreatePickupTimeFromString($str,$format,$separator){
          $fromString = substr($str, 0, strlen($str)-strpos($str,$separator)-1);
          $tillString = substr($str, strpos($str,$separator)+1); 
          
          $fromDateTime = DateTimeImmutable::createFromFormat($format,$fromString);
          $toDateTime = DateTimeImmutable::createFromFormat($format,$tillString);
          return PickupTime::CreatePickupTime($fromDateTime, $toDateTime);
     }
     public static function GetMinPickupTime(){
          $currentDatetime = CreateDateTimeImmutableFromMutable(CurrentDateTime::Now());
          $currentHours = intval($currentDatetime->format('H'));
          $resultDate = null;
          
          if($currentHours < 8 && 
            !self::isWeekend($currentDatetime) &&
            !self::isNonWorkingDate($currentDatetime)){
                $resultDate = PickupTime::TodayMorning();
          }
          else if($currentHours >= 8 && $currentHours < 16 &&
                    !self::isWeekend($currentDatetime)&&
                    !self::isNonWorkingDate($currentDatetime))
          {
                $resultDate = PickupTime::TodayEvening();
          }
          else{ //$currentHours >= 16 => next morning
                $resultDate = PickupTime::TodayEvening()->NextNearestPoint();
          }
         
          return $resultDate;
     }
    
    public static function GetPickupTimesForDuration(DateTimeImmutable $from, DateTimeImmutable $to){
            
        $pickup = self::CreateMorningPickup($from);
        $dates = []; 
        while($pickup->from <= $to){
            //Morning
            $dates[] = $pickup;
            
            //Evening
            $pickup = $pickup->NextNearestPoint();
            $dates[] = $pickup;
            
            $pickup = $pickup->NextNearestPoint();
        }
        
        return $dates;
    }
    
    public function asText(){
        return self::__toString();
    }
    
    public function formatRange($format,$separator){
        return $this->from->format($format).$separator.$this->to->format($format);
    }

    public function __toString()
    {
        return strftime("%d-%b-%Y | %k:%M", $this->from->getTimestamp()).' - '.strftime("%k:%M",$this->to->getTimestamp());
        // return  $this->from->format("d-M-Y | h:i A").' - '.$this->to->format("h:i A");
    }
}


?>
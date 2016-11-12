<?php
include_once(dirname(__FILE__)."/_helpers.php");
require_once(dirname(__FILE__).'/CurrentDateTime.class.php');

class NonworkingDays{

    public static function GetNonWokingDaysForFuturePeriod($days){
       $dates = [];

       $blockedDates = BlockedDate::GetForFuturePeriod($days);
       foreach($blockedDates as $blockedDate){
           $dates[] = ($blockedDate->Date);
       }

       $blockedDates2 = BlockedDay::GetAndConvertToDateForFuturePeriod($days);
       foreach($blockedDates2 as $date){
           $dates[] = $date;
       }

       return $dates;
    }
}

class BlockedDate{
    var $Description;

    var $Date;

    public static function GetForFuturePeriod($days){
        global $DBName;
         
        $minDate = CurrentDateTime::Now();
        $maxDate = CurrentDateTime::Now()->modify('+ '.$days.' days');
         
        $dates = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT * FROM `".$DBName."`.`blockeddate` 
                     WHERE `DATE` >= '".$minDate->format("Y-m-d")."'
                     AND `DATE` <= '".$maxDate->format("Y-m-d")."'";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $dates[] = self::createBlockedDateFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $dates;
    }

    public static function GetAll(){
        global $DBName;
         
        $dates = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT * FROM `".$DBName."`.`blockeddate`";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $dates[] = self::createBlockedDateFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $dates;
    }
    
    static function createBlockedDateFromRow($row){
        if(!$row["ID"]){
             return null;
         }
        
        $date =  new BlockedDate();
        $date->Date = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $row["DATE"]);
        $date->Description = $row["DESCRIPTION"];

        return $date;
    }
}


class BlockedDay{
    var $Description;
    var $Day;
    var $Month;

    public static function GetAndConvertToDateForFuturePeriod($days){
        $blockeddays = self::GetAll();
        $datesResult =[];

        $dateIteration = CreateDateTimeImmutableFromMutable(CurrentDateTime::Now()->setTime(0,0));
        for($i =0; $i<$days;$i++){
            foreach($blockeddays as $blockedday){
                if($blockedday->Month === intval($dateIteration->format("n"))  &&
                    $blockedday->Day === intval($dateIteration->format("j"))){
                        $datesResult[] = $dateIteration;
                        break;
                    }
            }

            $dateIteration = $dateIteration->modify('+ 1 day');
        }
        
        return $datesResult;
    }

    public static function GetAll(){
        global $DBName;
         
        $dates = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT * FROM `".$DBName."`.`blockedday`";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $dates[] = self::createBlockedDayFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $dates;
    }

    static function createBlockedDayFromRow($row){
        if(!$row["ID"]){
             return null;
         }
        
        $date =  new BlockedDay();
        $date->Day = intval($row["DAY"]);
        $date->Month = intval($row["MONTH"]);
        $date->Description = $row["DESCRIPTION"];

        return $date;
    }
}
?>
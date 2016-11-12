<?php 
include_once(dirname(__FILE__)."/_helpers.php");

class CityAndArea{

    /** @var int */
    var $CityId;
    
    /** @var int */
    var $CityAreaId;

    /** @var string */
    var $CityName;
  
    /** @var string */
    var $CityAreaName;


    public static function GetCityAndAreaByCityAreaId($cityAreaId){
        global $DBName;
         
        $result = null;
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT city_area.ID as CITY_AREA_ID, city_area.CITY_ID, city.NAME as CITY_NAME, city_area.NAME as CITY_AREA_NAME
            FROM `".$DBName."`.`city` join `".$DBName."`.`city_area`
                 on city_area.CITY_ID = city.ID
                 where city_area.ID = '".$mysqli->real_escape_string($cityAreaId)."'";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                  $row = $sql_result->fetch_assoc();

                  $result =  self::createCityAndAreaFromRow($row);
                  $sql_result->free();
            }
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    }
    
     private static function createCityAndAreaFromRow($row){
        $cityAndArea = new CityAndArea();
        $cityAndArea->CityAreaId = $row["CITY_AREA_ID"];
        $cityAndArea->CityAreaName = $row["CITY_AREA_NAME"];
        $cityAndArea->CityId = $row["CITY_ID"];
        $cityAndArea->CityName = $row["CITY_NAME"];

        return $cityAndArea;
    }

    public function GetFullName(){
        return $this->CityName." / ".$this->CityAreaName;
    }

    public function __toString()
    {
        return "CityAndArea";
    }
}


?>
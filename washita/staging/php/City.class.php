<?php 
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/CityArea.class.php");

class City{

    /** @var int */
    var $Id;
    
    /** @var string */
    var $Name;
    
    var $Areas = [];
    
    public static function GetAllCititesWithAreas(){
        global $DBName;
         
        $cities = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT city_area.ID as CITY_AREA_ID, city_area.CITY_ID, city.NAME as CITY_NAME, city_area.NAME as CITY_AREA_NAME
            FROM `".$DBName."`.`city` join `".$DBName."`.`city_area`
                 on city_area.CITY_ID = city.ID" ;
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $city = null;    
                    foreach ($cities as $addedCity) {
                        if($addedCity->Id == $row["CITY_ID"]){
                            $city = $addedCity;
                            break;
                        }                        
                    }
                    if($city == null){
                        $city = self::createCityFromRow($row);
                        $cities[] = $city;
                    }
                    $city->Areas[] =  self::createCityAreaFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $cities;
    }

    public static function GetCityById($cityId){
        global $DBName;
         
        $city = null;
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT ID as CITY_ID, NAME as CITY_NAME
                      FROM `".$DBName."`.`city` 
                      WHERE ID = ".$mysqli->real_escape_string($cityId);
            $sql_result = $mysqli->query($query);
            if($sql_result){
                  $row = $sql_result->fetch_assoc();
                  $city = self::createCityFromRow($row);

                  $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $city;
    }

    private static function createCityFromRow($row){
        $city = new City();
        $city->Id = $row["CITY_ID"];
        $city->Name = $row["CITY_NAME"];

        return $city;
    }     

     private static function createCityAreaFromRow($row){
        $cityArea = new CityArea();
        $cityArea->Id = $row["CITY_AREA_ID"];
        $cityArea->Name = $row["CITY_AREA_NAME"];

        return $cityArea;
    }

    public function __toString()
    {
        return "City";
    }
}


?>
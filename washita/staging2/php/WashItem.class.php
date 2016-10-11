<?php 
include_once(dirname(__FILE__)."/_helpers.php");

class WashItem{

    /** @var int */
    var $Id;
    
    /** @var string */
    var $Name;
  
    /** @var decimal */
    var $Weight;

    /** @var decimal */
    var $DryCleanPrice;
    /** @var decimal */
    var $SpecialCleanPrice;

    var $ImageUri;
    
    public static function GetAll(){
        global $DBName;
         
        $result = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT ID, NAME, ITEM_WEIGHT, ITEM_DRY_CLEAN_PRICE, ITEM_SPECIAL_CLEAN_PRICE, IMAGE_FILE_NAME
            FROM `".$DBName."`.`wash_item`";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $result[] = self::createOrderFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    }
    
   
    
     private static function createOrderFromRow($row){
         if(!$row["ID"]){
             return null;
         }

        global $site_root;

        $washItem = new WashItem();
                
        $washItem->Id = $row["ID"];
        $washItem->Name = $row["NAME"];
        $washItem->Weight = $row["ITEM_WEIGHT"];
        $washItem->DryCleanPrice = $row["ITEM_DRY_CLEAN_PRICE"];
        $washItem->SpecialCleanPrice = $row["ITEM_SPECIAL_CLEAN_PRICE"];
        
        $washItem->ImageUri = isset($row["IMAGE_FILE_NAME"])? $site_root.'/uploads/'.$row["IMAGE_FILE_NAME"] : "";

        return $washItem;
    }
    
   
    public function __toString()
    {
        return "WashItem";
    }
}


?>
<?php 
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/WashItem.class.php");

class OrderWashItemLine{
    /** @var string */
    var $OrderNumber;
    
    /** @var WashItem */
    var $WashItem;
  
    /** @var decimal */
    var $Count;
    
    public static function GetCurrentItemsForOrder($orderNumber){
        $result = self::GetActualItemsForOrder($orderNumber);
        if(empty($result)){
            $result = self::GetInitialItemsForOrder($orderNumber);
        }

        return $result;
    }

    public static function GetInitialItemsForOrder($orderNumber){
        global $DBName;
         
        $result = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT wash_item.ID, wash_item.NAME, wash_item.ITEM_WEIGHT, wash_item.ITEM_DRY_CLEAN_PRICE, wash_item.ITEM_SPECIAL_CLEAN_PRICE,
                        order_wash_items.ORDER_NUMBER, order_wash_items.COUNT, order_wash_items.IS_ACTUAL 
            FROM `".$DBName."`.`wash_item`
            JOIN `".$DBName."`.`order_wash_items` on (wash_item.ID = order_wash_items.WASH_ITEM_ID)
            WHERE order_wash_items.ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'
                  AND order_wash_items.IS_ACTUAL = 0";
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

    public static function GetActualItemsForOrder($orderNumber){
        global $DBName;
         
        $result = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT wash_item.ID, wash_item.NAME, wash_item.ITEM_WEIGHT, wash_item.ITEM_DRY_CLEAN_PRICE, wash_item.ITEM_SPECIAL_CLEAN_PRICE,
                        order_wash_items.ORDER_NUMBER, order_wash_items.COUNT, order_wash_items.IS_ACTUAL 
            FROM `".$DBName."`.`wash_item`
            JOIN `".$DBName."`.`order_wash_items` on (wash_item.ID = order_wash_items.WASH_ITEM_ID)
            WHERE order_wash_items.ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'
                  AND order_wash_items.IS_ACTUAL = 1";
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


    public static function SetActualItemsForOrder($orderNumber, $actualWashItemLines){
        global $DBName;
         try{
            $mysqli = OpenMysqlConnection();

            $query = "DELETE from `".$DBName."`.`order_wash_items`
                      WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'
                       AND IS_ACTUAL = 1";
            if(!$mysqli->query($query)){
                echo "Error DB1. Cannot update actual wash items!";
                exit(); 
            }  

            if(count($actualWashItemLines) > 0){
                $query = " INSERT INTO `".$DBName."`.`order_wash_items` (ORDER_NUMBER, WASH_ITEM_ID, COUNT, IS_ACTUAL)";
                for ($x = 0; $x < count($actualWashItemLines); $x++) {
                    if($x != 0){// not first item
                        $query.= " UNION ALL ";
                    }
                    $orderWashItemLine = $actualWashItemLines[$x];
                    $query.= " SELECT '".$mysqli->real_escape_string($orderNumber)."', 
                            '".$orderWashItemLine->WashItem->Id."', '".$orderWashItemLine->Count."', 1";
                } 

                if(!$mysqli->query($query)){
                    echo $query;
                    echo "Error DB2. Cannot update actual wash items!";
                    exit(); 
                } 
            }
            
         
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

    }
    
    public static function AddInitialItemsToOrder($orderNumber, $orderWashItemLines){
        global $DBName;
         
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = " INSERT INTO `".$DBName."`.`order_wash_items` (ORDER_NUMBER, WASH_ITEM_ID, COUNT)";
            for ($x = 0; $x < count($orderWashItemLines); $x++) {
                if($x != 0){// not first item
                     $query.= " UNION ALL ";
                }
                $orderWashItemLine = $orderWashItemLines[$x];
                $query.= " SELECT '".$mysqli->real_escape_string($orderNumber)."', 
                         '".$orderWashItemLine->WashItem->Id."', '".$orderWashItemLine->Count."'";
            } 
            
            //echo $query;
            // BUILT
            // INSERT INTO dbo.MyTable (ID, Name)
            // SELECT 123, 'Timmy'
            // UNION ALL
            // SELECT 124, 'Jonny'
            if($mysqli->query($query)){
                // $result->success = true;
            }
         
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

    }

    public static function ConvertFromPost($washType, $washitems){
            $orderWashItemLines = [];
            if(!empty($washitems)) {
                $dbWahItems = WashItem::GetAllByType($washType);
                foreach ($washitems as $str) {
                    $obj = explode(",",$str);
                    $line = new OrderWashItemLine();
                    $line->Count = $obj[1];
                    foreach ($dbWahItems as $dbWahItem) {
                        if($dbWahItem->Id == $obj[0]){
                            $line->WashItem = $dbWahItem;
                            break;
                        }
                    }
                    $orderWashItemLines[] = $line;
                }
            }

            return $orderWashItemLines;
    }
    
     private static function createOrderFromRow($row){
         if(!$row["ID"]){
             return null;
         }
        $orderWashItemLine = new OrderWashItemLine();
        $orderWashItemLine->OrderNumber = $row["ORDER_NUMBER"];
        $orderWashItemLine->Count = $row["COUNT"];
        
        $washItem = new WashItem();
        $washItem->Id = $row["ID"];
        $washItem->Name = $row["NAME"];
        $washItem->Weight = $row["ITEM_WEIGHT"];
        $washItem->DryCleanPrice = $row["ITEM_DRY_CLEAN_PRICE"];
        $washItem->SpecialCleanPrice = $row["ITEM_SPECIAL_CLEAN_PRICE"];
        
        $orderWashItemLine->WashItem = $washItem;
        
        return $orderWashItemLine;
    }

    public static function WashItemLineToStr($a){
        return $a->Count."x ".$a->WashItem->Name;
    }
    public static function LinesToString($washItemLines){
            return count($washItemLines)>0? implode(", ", array_map("self::WashItemLineToStr",$washItemLines)) : '-';
    }

    public static function WashItemLineToIdStr($a){
        return $a->Id;
    }
    public static function LineIdsToString($washItemLines){
            return count($washItemLines)>0? implode(",", array_map("self::WashItemLineToIdStr",$washItemLines)) : '';
    }
    
   
    public function __toString()
    {
        return "OrderWashItemLine";
    }
}


?>
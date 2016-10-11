<?php 
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/WashType.enum.php");

class OrderCustomItemLine{
    var $WashType;
    /** @var decimal */
    var $Count;
    
    var $Name;


    public static function ConvertFromPost($washType, $ironingPostItems){
            $orderIroningItemLines = [];
            if(!empty($ironingPostItems)) {
                foreach ($ironingPostItems as $str) {
                    $obj = explode(",",$str);
                    $line = new OrderCustomItemLine();
                    $line->WashType = $washType;
                    $line->Count = (int)($obj[0]);
                    $line->Name = $obj[1];
                        
                    $orderIroningItemLines[] = $line;
                }
            }

            return $orderIroningItemLines;
    }


    public static function GetCurrentItemsForOrder($washType, $orderNumber, $isActual){
        global $DBName;
         
        $result = [];
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT WASH_TYPE, NAME, COUNT, IS_ACTUAL 
            FROM `".$DBName."`.`order_custom_wash_items`
            WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'
                  AND IS_ACTUAL = ".($isActual? 1: 0)."
                  AND WASH_TYPE = ".$washType;
            $sql_result = $mysqli->query($query);
            if($sql_result){
                while($row = $sql_result->fetch_assoc()) {
                    $result[] = self::createOrderLineFromRow($row);
                }
                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    }

    public static function SetCustomOrderItems($orderNumber, $customItemLines, $isActual){
        global $DBName;
         
         try{
            $mysqli = OpenMysqlConnection(); 

             $query = "DELETE from `".$DBName."`.`order_custom_wash_items`
                      WHERE ORDER_NUMBER='".$mysqli->real_escape_string($orderNumber)."'
                       AND IS_ACTUAL = ".($isActual? 1: 0);

            if(!$mysqli->query($query)){
                echo "Error DB1. Cannot update actual wash items!";
                exit(); 
            }  

            $query = " INSERT INTO `".$DBName."`.`order_custom_wash_items` 
                        (ORDER_NUMBER, WASH_TYPE, NAME, COUNT, IS_ACTUAL)";
            for ($x = 0; $x < count($customItemLines); $x++) {
                if($x != 0){// not first item
                     $query.= " UNION ALL ";
                }
                $customItemLine = $customItemLines[$x];
                $query.= " SELECT '".$mysqli->real_escape_string($orderNumber)."', 
                         ".$customItemLine->WashType.",
                         '".$customItemLine->Name."', 
                         '".$customItemLine->Count."',
                         ".($isActual? 1: 0);
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
    
    private static function createOrderLineFromRow($row){

        $customItemLine = new OrderCustomItemLine();
        $customItemLine->Name = $row["NAME"];
        $customItemLine->Count = $row["COUNT"];
        $customItemLine->WashType = $row["WASH_TYPE"];
        
        
        return $customItemLine;
    }

    public static function CustomItemLineToStr($a){
        return $a->Count."x ".$a->Name;
    }
    public static function LinesToString($customItemLines){
            return count($customItemLines)>0? implode(", ", array_map("self::CustomItemLineToStr",$customItemLines)) : '-';
    }
   
    public function __toString()
    {
        return "OrderCustomItemLine";
    }
}


?>
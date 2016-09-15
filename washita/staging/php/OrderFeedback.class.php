<?php

require_once(dirname(__FILE__)."/Result.class.php");

 class OrderFeedback{
    var $Id;
    var $OrderNumber;
    var $Text;
    var $FeedbackCode;
    
    var $RatingOverall;
    var $RatingEasiness;
    var $RatingIroning;
    var $RatingWashing;
    var $RatingRecommend;
    var $RatingPickup;
    
    
    
    public static function SaveInDb(OrderFeedback $orderFeedback){
        global $DBName;
        
        $result = new Result();
        $id = null; 
        if(!$orderFeedback){
            return null;
        }
         try{
            $mysqli = OpenMysqlConnection(); 
            if(!$orderFeedback->Id){
                $query = "INSERT `".$DBName."`.`order_feedback`(RATING,TEXT,ORDER_NUMBER,FEEDBACK_CODE,RATING_OVERALL,RATING_EASINESS, RATING_IRONING,RATING_WASHING,RATING_RECOMMEND,RATING_PICKUP)
                            VALUES('".$mysqli->real_escape_string($orderFeedback->GetRating())."',
                                '".$mysqli->real_escape_string($orderFeedback->Text)."',
                                '".$mysqli->real_escape_string($orderFeedback->OrderNumber)."',
                                '".$mysqli->real_escape_string($orderFeedback->FeedbackCode)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingOverall)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingEasiness)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingIroning)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingWashing)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingRecommend)."',
                                '".$mysqli->real_escape_string($orderFeedback->RatingPickup)."'
                                )
                        ";
                        
                if($mysqli->query($query)){
                    $result->success = true;
                    $result->value = $mysqli->insert_id;
                    $orderFeedback->Id = $mysqli->insert_id;
                }
            }
            else{
                $query = "UPDATE `".$DBName."`.`order_feedback` 
                      SET RATING = '".$mysqli->real_escape_string($orderFeedback->GetRating())."',
                          TEXT = '".$mysqli->real_escape_string($orderFeedback->Text)."',
                          ORDER_NUMBER = '".$mysqli->real_escape_string($orderFeedback->OrderNumber)."',
                          FEEDBACK_CODE = '".$mysqli->real_escape_string($orderFeedback->FeedbackCode)."',
                          RATING_OVERALL = '".$mysqli->real_escape_string($orderFeedback->RatingOverall)."',
                          RATING_EASINESS = '".$mysqli->real_escape_string($orderFeedback->RatingEasiness)."',
                          RATING_IRONING = '".$mysqli->real_escape_string($orderFeedback->RatingIroning)."',
                          RATING_WASHING = '".$mysqli->real_escape_string($orderFeedback->RatingWashing)."',
                          RATING_RECOMMEND = '".$mysqli->real_escape_string($orderFeedback->RatingRecommend)."',
                          RATING_PICKUP = '".$mysqli->real_escape_string($orderFeedback->RatingPickup)."'
                          WHERE ID = '".$mysqli->real_escape_string($orderFeedback->Id)."'
                      ";
                if($mysqli->query($query)){
                    $result->success = true;
                }
            }
            $mysqli->close();
        }
        catch(Exception $e) {
            $result = Result::Fail("Internal error 'Save feedback DB'");
        }

        return $result;
    }
    
    public static function GetOrderFeedbackByOrderNumber($orderNumber){
          global $DBName;
         
        if(!$orderNumber){
            return null;
        }
        $orderFeedback = null;
         try{
            $mysqli = OpenMysqlConnection(); 
            $query = "SELECT *
            FROM `".$DBName."`.`order_feedback` 
            WHERE ORDER_NUMBER = '".$mysqli->real_escape_string($orderNumber)."'";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                if($row['ID']){
                    $orderFeedback = new OrderFeedback();
                    $orderFeedback->Id = $row['ID'];
                    $orderFeedback->OrderNumber = $row['ORDER_NUMBER'];
                    $orderFeedback->Text = $row['TEXT'];
                    $orderFeedback->FeedbackCode = $row['FEEDBACK_CODE'];
                    
                    $orderFeedback->RatingOverall = $row['RATING_OVERALL'];
                    $orderFeedback->RatingEasiness = $row['RATING_EASINESS'];
                    $orderFeedback->RatingIroning = $row['RATING_IRONING'];
                    $orderFeedback->RatingWashing = $row['RATING_WASHING'];
                    $orderFeedback->RatingRecommend = $row['RATING_RECOMMEND'];
                    $orderFeedback->RatingPickup = $row['RATING_PICKUP'];
                }

                $sql_result->free();
            }
            
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $orderFeedback;
    }
   
   
   public function SetRating($RatingOverall,$RatingEasiness, $RatingIroning, $RatingWashing, $RatingRecommend, $RatingPickup){
        $this->RatingOverall = $RatingOverall;
        $this->RatingEasiness = $RatingEasiness;
        $this->RatingIroning = $RatingIroning;
        $this->RatingWashing = $RatingWashing;
        $this->RatingRecommend = $RatingRecommend;
        $this->RatingPickup = $RatingPickup;
   }
   
   public function GetRating(){
        $sum = 0;
        $rating = array($this->RatingOverall,$this->RatingEasiness, $this->RatingIroning, $this->RatingWashing,$this->RatingRecommend,$this->RatingPickup);
        foreach ($rating as $rate) {
            if($rate){
                 $sum += $rate;
            }
        }
        
        return $sum/count($rating);       
   }
}

?>

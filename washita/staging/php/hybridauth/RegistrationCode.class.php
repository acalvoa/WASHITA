<?php
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/RegistrationCode.class.php");


class RegistrationCode{
    /**
     * @var string
     */
    public $Code;
    /**
     * @var UserType
     */
    public $UserType;
    /**
     * @var Boolean
     */
    public $IsUsed;

    public $InitialPersonalDiscount;


    public static function GetRegistrationCode($code){
        global $DBName;
        if(empty($code)){
            return null;
        }

        $result = null;
        try{
            $mysqli = OpenMysqlConnection(); 
             $query = "SELECT 
                             registration_code.ID as registration_codeID, 
                             registration_code.CODE as registration_codeCODE,
                             registration_code.USER_TYPE as registration_codeUSER_TYPE, 
                             registration_code.IS_USED as registration_codeIS_USED,
                             registration_code.INITIAL_PERSONAL_DISCOUNT as registration_codeINITIAL_PERSONAL_DISCOUNT
                  FROM `".$DBName."`.`registration_code` WHERE `CODE`='".$mysqli->real_escape_string($code)."' LIMIT 1";

            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                $result = self::CreateRegistrationCodeFromRow($row);
                $sql_result->free();
            }
            $mysqli->close();
        }
        catch(Exception $e) {
            
        }

        return $result;
    }

   

     public static function CreateRegistrationCodeFromRow($row){
         if(!$row["registration_codeID"]){
             return null;
         }

        $registrationCode = new RegistrationCode();

        $registrationCode->Code = $row["registration_codeCODE"];
        $registrationCode->UserType = $row["registration_codeUSER_TYPE"];
        $registrationCode->IsUsed = $row["registration_codeIS_USED"];
        $registrationCode->InitialPersonalDiscount = $row["registration_codeINITIAL_PERSONAL_DISCOUNT"];

        return $registrationCode;
     }    

    public static function SetRegistrationCodeAsUsed($code){
          global $DBName;
        try{
            $mysqli = OpenMysqlConnection(); 
            // Write to the database requested data
            $query = "UPDATE `".$DBName."`.`registration_code`
                    SET IS_USED=1
                    WHERE CODE='".$mysqli->real_escape_string($code)."'";

            $mysqli->query($query);
            if($mysqli){
                $mysqli->close();
            }   
        }
        catch(Exception $e) {
                
        }
    }


    public function __toString() {
        return "RegistrationCode";
    }
}

?>